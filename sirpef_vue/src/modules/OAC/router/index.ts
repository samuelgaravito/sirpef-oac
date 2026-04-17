export default [
    {
        path: '/oac/memos',
        name: 'oac.memos.create',
        component: () => import('../views/Memos/MemoCreate.vue'),
        meta: {
            title: 'Generar Memorándum',
            middleware: []
        }
    }
]
