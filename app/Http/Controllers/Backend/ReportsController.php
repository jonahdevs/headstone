<?php

namespace App\Http\Controllers\Backend;

use App\Exports\ReportsExport;
use App\Http\Controllers\Controller;
use App\Models\Memorial;
use App\Models\Order;
use App\Models\Transaction;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Number;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Maatwebsite\Excel\Excel;
use Spatie\Browsershot\Browsershot;

class ReportsController extends Controller
{
    public function index(Request $request, string $resource = '')
    {
        $filters = $request->only(['status', 'role', 'payment_status', 'start_date', 'end_date']);
        $resources = [
            'users' => [
                'filters' => [
                    'status' => [
                        'active',
                        'inactive',
                        'banned'
                    ],
                    'role' => [
                        'admin',
                        'manager',
                    ],
                ]
            ],
            'orders' => [
                'filters' => [
                    'status' => ['pending', 'processing', 'completed', 'cancelled'],
                    'payment_status' => ['abandoned', 'failed', 'ongoing', 'pending', 'processing', 'queued', 'reversed', 'success'],
                ]
            ],
            'transactions' => [
                'filters' => [
                    'status' => ['abandoned', 'failed', 'ongoing', 'pending', 'processing', 'queued', 'reversed', 'success'],
                ]
            ],
            'customers' => [
                'filters' => [
                    'status' => [
                        'active',
                        'inactive',
                        'banned'
                    ],
                ]
            ],
            'memorials' => [
                'filters' => [
                    'status' => [
                        'draft',
                        'published',
                    ],
                ]
            ]
        ];

        $data = $this->getResourceData($resource, $filters);

        return Inertia::render('backend/reports/Index', [
            'resource' => $resource,
            'resources' => array_keys($resources),
            'filters' => $request->only(array_keys($resources[$resource]['filters'] ?? [])),
            'resourceFilters' => $resources[$resource]['filters'] ?? [],
            'data' => $data,
        ]);
    }

    protected function getResourceData(string $resource, array $filters = [])
    {

        $startDate = !empty($filters['start_date']) ? Carbon::parse($filters['start_date']) : null;
        $endDate = !empty($filters['end_date']) ? Carbon::parse($filters['end_date']) : null;

        if ($resource === 'users') {
            $users = User::operators()->with('roles')
                ->when($filters['status'] ?? null, fn(Builder $query, $status) => $query->where('status', $status))
                ->when($filters['role'] ?? null, function (Builder $query, $role) {
                    $query->whereHas('roles', function (Builder $query) use ($role) {
                        $query->where('name', $role);
                    });
                })
                ->when($startDate && $endDate, fn($q) => $q->whereBetween('created_at', [$startDate, $endDate]))
                ->latest()->get();

            $data = $users->map(function ($user) {
                return (object) [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'status' => $user->status,
                    'joined_on' => $user->created_at->toFormattedDateString(),
                ];
            });
        } elseif ($resource === 'customers') {

            $customers = User::customers()
                ->when($filters['status'] ?? null, fn(Builder $query, $status) => $query->where('status', $status))
                ->when($startDate && $endDate, fn($q) => $q->whereBetween('created_at', [$startDate, $endDate]))
                ->latest()->get();

            $data = $customers->map(function ($customer) {
                return (object) [
                    'id' => $customer->id,
                    'name' => $customer->name,
                    'email' => $customer->email,
                    'phone' => $customer->phone,
                    'status' => $customer->status,
                    'joined_on' => $customer->created_at->toFormattedDateString(),
                ];
            });
        } elseif ($resource === 'orders') {
            $orders = Order::withCount('memorials')
                ->with('payment', 'customer')
                ->when($filters['status'] ?? null, fn(Builder $query, $status) => $query->where('status', $status))
                ->when($filters['payment_status'] ?? null, function (Builder $query, $status) {
                    $query->whereHas('payment', fn(Builder $query) => $query->where('status', $status));
                })
                ->when($startDate && $endDate, fn($q) => $q->whereBetween('created_at', [$startDate, $endDate]))
                ->latest()->get();

            $data = $orders->map(function ($order) {
                return (object) [
                    'id' => $order->id,
                    'status' => $order->status,
                    'date' => $order->created_at->format('F j, Y h:i A'),
                    'total' => Number::currency($order->total, 'kes'),
                    'items' => $order->memorials_count,
                    'payment_status' => $order->payment ? $order->payment->status : '',
                    'customer' => $order->customer ? $order->customer->email : '',
                ];
            });

        } elseif ($resource === 'transactions') {
            $transactions = Transaction::with('customer')
                ->when($filters['status'] ?? null, fn(Builder $query, $status) => $query->where('status', $status))
                ->when($startDate && $endDate, fn($q) => $q->whereBetween('created_at', [$startDate, $endDate]))
                ->latest()->get();

            $data = $transactions->map(function ($transaction) {
                return (object) [
                    "id" => $transaction->id,
                    'transaction_id' => $transaction->transaction_id,
                    'amount' => Number::currency($transaction->amount, 'kes'),
                    'date' => $transaction->created_at->format('F j, Y h:i A'),
                    'customer' => $transaction->customer ? $transaction->customer->email : '',
                    'order_id' => $transaction->order_id,
                    'status' => $transaction->status,
                ];
            });
        } elseif ($resource === 'memorials') {
            $memorials = Memorial::with('orders')
                ->when($startDate && $endDate, fn($q) => $q->whereBetween('created_at', [$startDate, $endDate]))
                ->latest()->get();
            $data = $memorials->map(function ($memorial) {
                $sales = $memorial->orders->sum(function ($order) {
                    $total = $order->pivot->total;
                    return $total;
                });

                return (object) [
                    'id' => $memorial->id,
                    'title' => $memorial->title,
                    'price' => Number::currency($memorial->price, 'kes'),
                    'sale_price' => $memorial->sale_price ? Number::currency($memorial->sale_price, 'kes') : null,
                    'sales' => Number::currency($sales ?? 0, 'kes'),
                ];
            });
        } else {
            $data = [];
        }

        return $data;
    }


    public function generate(Request $request, Browsershot $browsershot, Excel $excel, string $resource)
    {

        $filters = $request->query('filters', []);
        $data = $this->getResourceData($resource, $filters);
        $documentType = $request->query('documentType', 'pdf');

        if ($documentType == 'pdf') {
            $payload = (object) [
                'name' => 'Everstone',
                'address' => 'Thika Town, Kiambu County, Kenya',
                'phone' => '+254 700 000 000',
                'email' => 'support@everstone.co.ke',
                'date' => now()->format('d M Y'),
                'resource' => $resource,
                'data' => $data,
            ];

            // $html = view('reports', ['payload' => $payload])->render();

            // Generate PDF from the HTML
            // $pdfContent = $browsershot->html($html)
            //     ->showBackground()
            //     ->landscape()
            //     ->margins(4, 0, 4, 0)
            //     ->pdf();

            $pdf = Pdf::loadView('reports', ['payload' => $payload])->setPaper('A4', 'landscape');
            // Generate a download response
            $fileName = 'report-' . Str::random(6) . '.pdf';

            // return response($pdfContent, 200, [
            //     'Content-Type' => 'application/pdf',
            //     'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
            // ]);

            return $pdf->download($fileName);
        } else if ($documentType === 'excel' || $documentType === 'csv') {

            $data = json_decode($data, true);
            $export = new ReportsExport($data);
            $ext = $documentType === 'csv' ? 'csv' : 'xlsx';

            return $excel->download($export, "$resource-report.$ext");
        }

        return;
    }


}
