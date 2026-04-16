export const obtenerHoraActual = () => {
    const fechaActual = new Date();
    // Extraer las horas, minutos y segundos
    const horas = fechaActual.getHours();
    const minutos = fechaActual.getMinutes();
    // Devolver la hora formateada
    return `${horas <= 9 ? `0${horas}` : horas}:${minutos.toString().padStart(2, '0')}:00`;
  }