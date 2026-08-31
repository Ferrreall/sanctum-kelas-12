<template>
  <div class="container">
    <RouterLink to="/kelola-genre" class="btn-back">← Kembali</RouterLink>
    <h1>➕ Tambah Genre Baru</h1>

    <div class="form-wrapper">
      <form @submit.prevent="submitGenre">
        <div class="form-group">
          <label>Nama Genre <span class="required">*</span></label>
          <input v-model="form.nama_genre" type="text" placeholder="Contoh: Action" required class="form-input" />
        </div>
        <button type="submit" :disabled="loading" class="btn-submit">
          <span v-if="loading">⏳ Menyimpan...</span>
          <span v-else>💾 Simpan Genre</span>
        </button>
        <p v-if="errorMsg" class="error-msg">{{ errorMsg }}</p>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref }               from 'vue'
import { useRouter, RouterLink } from 'vue-router'
import api                   from '../../../utils/api'

const router   = useRouter()
const form     = ref({ nama_genre: '' })
const loading  = ref(false)
const errorMsg = ref('')

const submitGenre = async () => {
  try {
    loading.value  = true
    errorMsg.value = ''
    await api.post('/genre', form.value)
    router.push('/kelola-genre')
  } catch (err) {
    errorMsg.value = err.response?.data?.message || 'Terjadi kesalahan.'
  } finally {
    loading.value = false
  }
}
</script>

<style scoped>
/* Container Layout */
.container {
  display: flex;
  flex-direction: column;
  align-items: center;
  width: 100%;
  max-width: 600px;
  margin: 0 auto;
  padding: 32px 16px;
  box-sizing: border-box;
}

/* Button Back */
.btn-back {
  align-self: flex-start;
  display: inline-flex;
  align-items: center;
  gap: 8px;
  padding: 8px 16px;
  background-color: #ffffff;
  color: #4a5568;
  font-size: 14px;
  font-weight: 600;
  text-decoration: none;
  border-radius: 10px;
  border: 1px solid #e2e8f0;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.04);
  transition: all 0.2s ease-in-out;
  margin-bottom: 20px;
}

.btn-back:hover {
  background-color: #f8fafc;
  color: #e94560;
  border-color: #cbd5e1;
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.08);
  transform: translateX(-3px);
}

h1 {
  width: 100%;
  font-size: 24px;
  font-weight: 700;
  color: #1a1a2e;
  margin-bottom: 24px;
  text-align: left;
}

/* Form Wrapper Card */
.form-wrapper {
  width: 100%;
  background: white;
  border-radius: 16px;
  padding: 32px;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
  box-sizing: border-box;
}

.form-group {
  display: flex;
  flex-direction: column;
  gap: 8px;
  margin-bottom: 24px;
  text-align: left;
}

.form-group label {
  font-size: 14px;
  font-weight: 600;
  color: #4a5568;
}

.required {
  color: #e94560;
}

.form-input {
  width: 100%;
  padding: 12px 16px;
  border: 1px solid #cbd5e1;
  border-radius: 10px;
  font-size: 14px;
  color: #1a1a2e;
  outline: none;
  transition: all 0.2s;
  box-sizing: border-box;
}

.form-input:focus {
  border-color: #e94560;
  box-shadow: 0 0 0 3px rgba(233, 69, 96, 0.15);
}

/* Submit Button */
.btn-submit {
  width: 100%;
  padding: 12px;
  background: #e94560;
  color: white;
  border: none;
  border-radius: 10px;
  font-size: 15px;
  font-weight: 600;
  cursor: pointer;
  transition: background 0.2s;
  display: flex;
  justify-content: center;
  align-items: center;
}

.btn-submit:hover {
  background: #d03750;
}

.btn-submit:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

.error-msg {
  margin-top: 16px;
  color: #dc2626;
  font-size: 14px;
  text-align: center;
  background: #fef2f2;
  padding: 10px;
  border-radius: 8px;
  border: 1px solid #fecaca;
}
</style>