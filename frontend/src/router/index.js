import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '../stores/auth'

const routes = [
  {
    path: '/',
    name: 'Landing',
    component: () => import('../views/LandingView.vue'),
  },
  {
    path: '/login',
    name: 'Login',
    component: () => import('../views/LoginView.vue'),
    meta: { requiresGuest: true },
  },
  {
    path: '/',
    component: () => import('../layouts/AdminLayout.vue'),
    children: [
      {
        path: 'dashboard',
        name: 'Dashboard',
        component: () => import('../views/DashboardView.vue'),
        meta: { title: 'Dashboard', requiresAuth: true },
      },
      {
        path: 'categories',
        name: 'Categories',
        component: () => import('../views/CategoriesView.vue'),
        meta: { title: 'Manajemen Kategori', requiresAuth: true },
      },
      {
        path: 'authors',
        name: 'Authors',
        component: () => import('../views/AuthorsView.vue'),
        meta: { title: 'Manajemen Penulis/Penerbit', requiresAuth: true },
      },
      {
        path: 'books',
        name: 'Books',
        component: () => import('../views/BooksView.vue'),
        meta: { title: 'Manajemen Buku/Komik', requiresAuth: true },
      },
      {
        path: 'members',
        name: 'Members',
        component: () => import('../views/MembersView.vue'),
        meta: { title: 'Manajemen Anggota', requiresAuth: true },
      },
      {
        path: 'rentals',
        name: 'Rentals',
        component: () => import('../views/RentalsView.vue'),
        meta: { title: 'Manajemen Peminjaman', requiresAuth: true },
      },
    ],
  },
  {
    path: '/:pathMatch(.*)*',
    redirect: '/',
  },
]

const router = createRouter({
  history: createWebHistory(),
  routes,
})

router.beforeEach((to) => {
  const auth = useAuthStore()

  if (to.meta.requiresAuth && !auth.isAuthenticated) {
    return { name: 'Login' }
  }
  if (to.meta.requiresGuest && auth.isAuthenticated) {
    return { name: 'Dashboard' }
  }
})

export default router
