const routes = [
  {
    path: '/',
    component: () => import('layouts/MainLayout.vue'),
    children: [
      { path: '/', component: () => import('pages/IndexPage.vue'), meta: { requiresAuth: true } },
      { path: '/usuarios', component: () => import('pages/usuarios/Usuarios.vue'), meta: { requiresAuth: true } },
      { path: '/datos-empresa', component: () => import('pages/empresa/DatosEmpresa.vue'), meta: { requiresAuth: true } },
      { path: '/mi-cuenta', component: () => import('pages/mi-cuenta/MiCuenta.vue'), meta: { requiresAuth: true } },
      { path: '/mascotas', component: () => import('pages/mascotas/Mascotas.vue'), meta: { requiresAuth: true } },
      { path: '/mascotas/create', component: () => import('pages/mascotas/MascotaCreate.vue'), meta: { requiresAuth: true } },
      { path: '/mascotas/:id', component: () => import('pages/mascotas/MascotaShow.vue'), meta: { requiresAuth: true } },
      { path: '/productos', component: () => import('pages/productos/Productos.vue'), meta: { requiresAuth: true } },
    ]
  },
  {
    path: '/login',
    component: () => import('layouts/AuthPage.vue')
  },
  // Always leave this as last one,
  // but you can also remove it
  {
    path: '/:catchAll(.*)*',
    component: () => import('pages/ErrorNotFound.vue')
  }
]

export default routes
