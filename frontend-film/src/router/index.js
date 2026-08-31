// ============================================================
// router/index.js — Konfigurasi halaman/navigasi aplikasi
// ============================================================
// Setiap "route" adalah mapping: URL → Komponen Vue yang tampil
// ============================================================

import { createRouter, createWebHistory } from 'vue-router'

// ─── Import semua halaman (views) ─────────────────────────
// Halaman Publik (bisa diakses tanpa login)
import HomeView     from '../views/Public/HomeView.vue'
import DetailFilm   from '../views/Public/DetailFilm.vue'

// Halaman Autentikasi
import LoginView    from '../views/Auth/LoginView.vue'


// ─── Daftar semua route ────────────────────────────────────
const routes = [
  // Halaman publik (bisa diakses tanpa login)
  { path: '/',           component: HomeView,   name: 'home' },
  { path: '/film/:id',   component: DetailFilm, name: 'detail-film' },
  { path: '/login',      component: LoginView,  name: 'login' },

  
]

// ─── Buat instance router ──────────────────────────────────
const router = createRouter({
  history: createWebHistory(),  // Pakai URL biasa (bukan /#/)
  routes,
})

// ─── Navigation Guard (Middleware) ────────────────────────
// Kode ini berjalan SETIAP KALI user berpindah halaman
router.beforeEach((to, from, next) => {
  // Cek apakah route yang dituju membutuhkan autentikasi
  if (to.meta.requiresAuth) {
    // Cek apakah ada token login di localStorage
    const token = localStorage.getItem('token')

    if (!token) {
      // Tidak ada token → paksa ke halaman login
      next('/login')
    } else {
      // Ada token → izinkan masuk
      next()
    }
  } else {
    // Route publik → langsung izinkan
    next()
  }
})

export default router