import { onMounted, ref } from 'vue';

type Appearance = 'light' | 'dark' | 'system';

// Shared appearance state
const appearance = ref<Appearance>('system');

export function updateTheme(value: Appearance) {
    if (typeof window === 'undefined') return;

    if (value === 'system') {
        const mediaQueryList = window.matchMedia('(prefers-color-scheme: dark)');
        const systemTheme = mediaQueryList.matches ? 'dark' : 'light';

        document.documentElement.classList.toggle('dark', systemTheme === 'dark');
    } else {
        document.documentElement.classList.toggle('dark', value === 'dark');
    }
}

const setCookie = (name: string, value: string, days = 365) => {
    if (typeof document === 'undefined') return;

    const maxAge = days * 24 * 60 * 60;
    document.cookie = `${name}=${value};path=/;max-age=${maxAge};SameSite=Lax`;
};

const mediaQuery = () => {
    if (typeof window === 'undefined') return null;
    return window.matchMedia('(prefers-color-scheme: dark)');
};

const getStoredAppearance = () => {
    if (typeof window === 'undefined') return null;
    return localStorage.getItem('appearance') as Appearance | null;
};

const handleSystemThemeChange = () => {
    const currentAppearance = getStoredAppearance();
    updateTheme(currentAppearance || 'system');
};

export function initializeTheme() {
    if (typeof window === 'undefined') return;

    const savedAppearance = getStoredAppearance();
    appearance.value = savedAppearance || 'system';

    updateTheme(appearance.value);
    mediaQuery()?.addEventListener('change', handleSystemThemeChange);
}

export function useAppearance() {
    onMounted(() => {
        const savedAppearance = localStorage.getItem('appearance') as Appearance | null;
        if (savedAppearance) {
            appearance.value = savedAppearance;
        }
    });

    function updateAppearance(value: Appearance) {
        appearance.value = value;
        localStorage.setItem('appearance', value);
        setCookie('appearance', value);
        updateTheme(value);
    }

    return {
        appearance,
        updateAppearance,
    };
}
