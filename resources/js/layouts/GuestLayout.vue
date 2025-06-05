<script setup>
import AppBar from '@/components/AppBar.vue';
import Footer from '@/components/Footer.vue';
import { Toaster } from 'vue-sonner';
import 'vue-sonner/style.css';
</script>

<template>
    <AppBar />

    <section class="min-h-[87svh]">
        <slot />
    </section>

    <Footer />
    <Toaster richColors closeButton />
</template>
