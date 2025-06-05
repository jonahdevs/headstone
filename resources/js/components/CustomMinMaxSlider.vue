<script setup>
import { computed, ref, watch } from 'vue';

const props = defineProps({
    min: { type: Number, default: 0 },
    max: { type: Number, default: 100 },
    step: { type: Number, default: 1 },
    modelValue: {
        type: Object,
        default: () => ({ min: 25, max: 75 }),
    },
});

const emit = defineEmits(['update:modelValue']);

const minVal = ref(props.modelValue.min);
const maxVal = ref(props.modelValue.max);

const percent = (val) => ((val - props.min) / (props.max - props.min)) * 100;

const trackStyle = computed(() => ({
    left: `${percent(minVal.value)}%`,
    width: `${percent(maxVal.value - minVal.value)}%`,
}));

const updateMin = (value) => {
    const newVal = Math.min(value, maxVal.value - props.step);
    minVal.value = newVal;
    emit('update:modelValue', { min: newVal, max: maxVal.value });
};

const updateMax = (value) => {
    const newVal = Math.max(value, minVal.value + props.step);
    maxVal.value = newVal;
    emit('update:modelValue', { min: minVal.value, max: newVal });
};

watch(
    () => props.modelValue,
    ({ min, max }) => {
        minVal.value = min;
        maxVal.value = max;
    },
);
</script>

<template>
    <div class="w-full space-y-6">
        <div class="relative h-1 rounded-full">
            <!-- active range track -->
            <div class="bg-primary absolute h-full rounded-full" :style="trackStyle"></div>

            <!-- min thumb -->
            <input
                type="range"
                :min="props.min"
                :max="props.max"
                :step="props.step"
                :value="minVal"
                @input="(e) => updateMin(parseFloat(e.target.value))"
                class="thumb-input z-20"
            />

            <!-- max thumb -->
            <input
                type="range"
                :min="props.min"
                :max="props.max"
                :step="props.step"
                :value="maxVal"
                @input="(e) => updateMax(parseFloat(e.target.value))"
                class="thumb-input z-10"
            />
        </div>

        <div class="flex flex-wrap items-center justify-between gap-4">
            <div class="flex items-center gap-2">
                <span class="text-muted-foreground text-sm">Ksh</span>
                <input
                    type="number"
                    :min="props.min"
                    :max="maxVal - props.step"
                    :step="props.step"
                    v-model.number="minVal"
                    @change="updateMin(minVal)"
                    class="border-input bg-background w-24 rounded-md border px-2 py-1 text-sm shadow-sm"
                />
            </div>
            <div class="flex items-center gap-2">
                <span class="text-muted-foreground text-sm">Ksh</span>
                <input
                    type="number"
                    :min="minVal + props.step"
                    :max="props.max"
                    :step="props.step"
                    v-model.number="maxVal"
                    @change="updateMax(maxVal)"
                    class="border-input bg-background w-24 rounded-md border px-2 py-1 text-sm shadow-sm"
                />
            </div>
        </div>
    </div>
</template>

<style scoped>
.thumb-input {
    position: absolute;
    width: 100%;
    height: 100%;
    appearance: none;
    background: transparent;
    pointer-events: none;
    top: 0;
}

.thumb-input::-webkit-slider-thumb {
    appearance: none;
    height: 1rem;
    width: 1rem;
    border-radius: 3px;
    background: white;
    cursor: pointer;
    pointer-events: all;
    position: relative;
    z-index: 10;
    border: 2px solid black;

    background-image: linear-gradient(to right, black 0.5px, transparent 3px, black 0.5px, transparent 3px, black 0.5px);
    background-size: 6px 50%;
    background-repeat: no-repeat;
    background-position: center;
}

.thumb-input::-moz-range-thumb {
    height: 1rem;
    width: 1rem;
    border-radius: 9999px;
    background: rgb(59, 130, 246);
    cursor: pointer;
    pointer-events: all;
    border: 2px solid white;
}
</style>
