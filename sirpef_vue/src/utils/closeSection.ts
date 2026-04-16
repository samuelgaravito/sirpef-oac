interface EstInterface {
    value: boolean
}

export const hidden = (Estado: any, e: PointerEvent) => {
    const target = e.target as HTMLElement
    if (e && target.tagName === "SECTION") {
        Estado.value = true 
    } else { 
        Estado.value = false
    }
}
