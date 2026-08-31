<template>
  <!-- Navbar tampil di SEMUA halaman -->
  <nav class="navbar">
    <div class="navbar-brand">
      <RouterLink to="/">🎬 CineVue</RouterLink>
    </div>

    <div class="navbar-menu">
      <!-- Menu untuk GUEST (belum login) -->
      <template v-if="!isLoggedIn">
        <RouterLink to="/">Home</RouterLink>
        <RouterLink to="/login" class="btn-nav-login">🔐 Login</RouterLink>
      </template>

      <!-- Menu untuk ADMIN (sudah login) -->
      <template v-else>
        <RouterLink to="/">Home</RouterLink>
        <RouterLink to="/dashboard">Dashboard</RouterLink>
        <RouterLink to="/kelola-film">Kelola Film</RouterLink>
        <RouterLink to="/tambah-film">+ Tambah Film</RouterLink>
        <span class="navbar-user">👤 {{ userName }}</span>
        <button @click="handleLogout" class="btn-nav-logout">🚪 Logout</button>
      </template>
    </div>
  </nav>

  <!-- Di sinilah setiap halaman (View) akan ditampilkan -->
  <RouterView />
</template>

<script setup>
import { ref, onMounted, watch } from 'vue'
import { RouterLink, RouterView, useRouter, useRoute } from 'vue-router'
import api from './utils/api'

const router     = useRouter()
const route      = useRoute()
const isLoggedIn = ref(false)
const userName   = ref('')

// ─── Fungsi cek status login dari localStorage ─────────────
const cekStatusLogin = () => {
  const token = localStorage.getItem('token')
  const user  = localStorage.getItem('user')

  if (token && user) {
    try {
      isLoggedIn.value = true
      userName.value   = JSON.parse(user).name
    } catch (e) {
      isLoggedIn.value = false
    }
  } else {
    isLoggedIn.value = false
    userName.value   = ''
  }
}

// Cek saat app pertama kali dibuka
onMounted(() => { cekStatusLogin() })

// Cek ulang setiap kali URL berubah → navbar langsung update!
watch(() => route.path, () => { cekStatusLogin() })

// ─── Fungsi Logout ─────────────────────────────────────────
const handleLogout = async () => {
  try {
    await api.post('/logout')   // Hapus token di server
  } catch (err) {
    console.warn('Logout API error:', err)
  } finally {
    localStorage.removeItem('token')   // Hapus token di browser
    localStorage.removeItem('user')

    isLoggedIn.value = false           // Update tampilan navbar
    userName.value   = ''

    router.push('/login')              // Redirect ke login
  }
}
</script>

<style scoped>
.navbar {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 14px 32px;
  background: linear-gradient(135deg, #1a1a2e, #16213e);
  box-shadow: 0 2px 12px rgba(0,0,0,0.3);
  position: sticky;
  top: 0;
  z-index: 100;
}

.navbar-brand a {
  color: #e94560;
  font-size: 22px;
  font-weight: 700;
  text-decoration: none;
  letter-spacing: 1px;
}

.navbar-menu {
  display: flex;
  align-items: center;
  gap: 20px;
}

.navbar-menu a {
  color: #ccc;
  text-decoration: none;
  font-size: 14px;
  font-weight: 500;
  transition: color 0.2s;
}

.navbar-menu a:hover,
.navbar-menu a.router-link-active {
  color: #e94560;
}

.navbar-user {
  color: #aaa;
  font-size: 13px;
  border-left: 1px solid #333;
  padding-left: 16px;
}

.btn-nav-login {
  background: #e94560 !important;
  color: white !important;
  padding: 8px 16px;
  border-radius: 8px;
  font-size: 13px !important;
  text-decoration: none;
}

.btn-nav-logout {
  background: transparent;
  color: #e74c3c;
  border: 1px solid #e74c3c;
  padding: 7px 14px;
  border-radius: 8px;
  font-size: 13px;
  font-weight: 600;
  cursor: pointer;
  font-family: inherit;
  transition: all 0.2s;
}

.btn-nav-logout:hover {
  background: #e74c3c;
  color: white;
}
</style>