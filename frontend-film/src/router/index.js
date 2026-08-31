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

// // Halaman Admin (harus login)
import DashboardView from '../views/Admin/DashboardView.vue'

// // Admin — Film
import TambahFilm   from '../views/Admin/Film/TambahFilm.vue'
import KelolaFilm   from '../views/Admin/Film/KelolaFilm.vue'
import EditFilm     from '../views/Admin/Film/EditFilm.vue'

// // Admin — Genre
import KelolaGenre  from '../views/Admin/Genre/KelolaGenre.vue'
import TambahGenre  from '../views/Admin/Genre/TambahGenre.vue'
import EditGenre    from '../views/Admin/Genre/EditGenre.vue'

// // Admin — Aktor
import KelolaAktor  from '../views/Admin/Aktor/KelolaAktor.vue'
import TambahAktor  from '../views/Admin/Aktor/TambahAktor.vue'
import EditAktor    from '../views/Admin/Aktor/EditAktor.vue'

// ─── Daftar semua route ────────────────────────────────────
const routes = [
  // Halaman publik (bisa diakses tanpa login)
  { path: '/',           component: HomeView,   name: 'home' },
  { path: '/film/:id',   component: DetailFilm, name: 'detail-film' },
  { path: '/login',      component: LoginView,  name: 'login' },

  // Halaman admin (butuh login) — ditandai meta: { requiresAuth: true }
  { path: '/dashboard',     component: DashboardView, name: 'dashboard',     meta: { requiresAuth: true } },

  // // Film
  { path: '/tambah-film',   component: TambahFilm,    name: 'tambah-film',   meta: { requiresAuth: true } },
  { path: '/kelola-film',   component: KelolaFilm,    name: 'kelola-film',   meta: { requiresAuth: true } },
  { path: '/edit-film/:id', component: EditFilm,      name: 'edit-film',     meta: { requiresAuth: true } },

  // // Genre
  { path: '/kelola-genre',     component: KelolaGenre, name: 'kelola-genre',    meta: { requiresAuth: true } },
  { path: '/tambah-genre',     component: TambahGenre, name: 'tambah-genre',    meta: { requiresAuth: true } },
  { path: '/edit-genre/:id',   component: EditGenre,   name: 'edit-genre',      meta: { requiresAuth: true } },

  // // Aktor
  { path: '/kelola-aktor',     component: KelolaAktor, name: 'kelola-aktor',    meta: { requiresAuth: true } },
  { path: '/tambah-aktor',     component: TambahAktor, name: 'tambah-aktor',    meta: { requiresAuth: true } },
  { path: '/edit-aktor/:id',   component: EditAktor,   name: 'edit-aktor',      meta: { requiresAuth: true } },
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