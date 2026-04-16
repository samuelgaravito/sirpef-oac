import { ref, onMounted, onUnmounted, readonly } from 'vue';

export function useBreakpoint() {
  const isMobile = ref(window.innerWidth < 768); // Punto de corte para móvil (md en Tailwind)

  const checkScreen = () => {
    isMobile.value = window.innerWidth < 768;
  };

  onMounted(() => {
    window.addEventListener('resize', checkScreen);
  });

  onUnmounted(() => {
    window.removeEventListener('resize', checkScreen);
  });

  return {
    isMobile: readonly(isMobile) // Usamos readonly para evitar modificaciones accidentales
  };
}