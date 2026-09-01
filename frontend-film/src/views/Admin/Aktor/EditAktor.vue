<template>
  <div class="container">
    <RouterLink to="/kelola-aktor" class="btn-back">← Kembali</RouterLink>
    <h1 style="margin: 12px 0 24px">✏️ Edit Aktor</h1>

    <p v-if="loadingData" class="loading-text">⏳ Memuat data...</p>

    <div v-else class="form-wrapper">
      <form @submit.prevent="submitAktor">
        <div class="form-group">
          <label>Nama Aktor <span class="required">*</span></label>
          <input v-model="form.nama_aktor" type="text" required class="form-input" />
        </div>

        <div class="form-group">
          <label>Gender <span class="required">*</span></label>
          <select v-model="form.jenis_kelamin" required class="form-input">
            <option value="" disabled>Pilih Gender</option>
            <option value="Laki-laki">Laki-laki</option>
            <option value="Perempuan">Perempuan</option>
          </select>
        </div>

        <div class="form-group">
          <label>Tanggal Lahir <span class="required">*</span></label>
          <input v-model="form.tanggal_lahir" type="date" required class="form-input" />
        </div>

        <div class="form-group">
          <label>Umur (Tahun)</label>
          <input v-model="form.umur" type="number" min="0" placeholder="Contoh: 58" class="form-input" />
        </div>

          <div class="form-group">
            <label>URL Foto Aktor</label>
            <input v-model="form.foto" type="url" placeholder="https://..." class="form-input" />
          </div>

        <button type="submit" :disabled="loading" class="btn-submit">
          <span v-if="loading">⏳ Menyimpan...</span>
          <span v-else>💾 Simpan Perubahan</span>
        </button>
        <p v-if="errorMsg" class="error-msg">{{ errorMsg }}</p>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted }            from 'vue'
import { useRouter, useRoute, RouterLink } from 'vue-router'
import api                            from '../../../utils/api'

const router  = useRouter()
const route   = useRoute()
const aktorId = route.params.id

const form        = ref({ 
  nama_aktor: '', 
  jenis_kelamin: '', 
  tanggal_lahir: '',
  umur: '',
  foto: ''
})
const loadingData = ref(true)
const loading     = ref(false)
const errorMsg    = ref('')

onMounted(async () => {
  try {
    const res     = await api.get('/aktor')
    const current = res.data.data.find(a => a.id == aktorId)
    if (current) {
      form.value.nama_aktor    = current.nama_aktor || ''
      form.value.jenis_kelamin = current.jenis_kelamin || ''
      form.value.tanggal_lahir = current.tanggal_lahir || ''
      form.value.umur          = current.umur || ''
      form.value.foto          = current.foto || ''
    } else { 
      router.push('/kelola-aktor') 
    }
  } catch (err) { 
    alert('Gagal memuat data') 
  } finally { 
    loadingData.value = false 
  }
})

const submitAktor = async () => {
  try {
    loading.value  = true
    errorMsg.value = ''
    await api.put(`/aktor/${aktorId}`, form.value)
    router.push('/kelola-aktor')
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
  text-align: left;
}

.loading-text {
  font-size: 16px;
  color: #64748b;
  margin-top: 20px;
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
  margin-bottom: 20px;
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
  background-color: #fff;
  transition: all 0.2s;
  box-sizing: border-box;
}

.form-input:focus {
  border-color: #e94560;
  box-shadow: 0 0 0 3px rgba(233, 69, 96, 0.15);
}

select.form-input {
  cursor: pointer;
}

/* Submit Button */
.btn-submit {
  width: 100%;
  padding: 12px;
  margin-top: 10px;
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