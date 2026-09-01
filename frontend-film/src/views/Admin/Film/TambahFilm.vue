<template>
    <div class="container">
        <div class="page-title">
          <RouterLink to="/dashboard" class="btn-back">← Dashboard</RouterLink>
        </div>
      <h1>➕ Tambah Film Baru</h1>

    <div v-if="successMsg" class="alert alert-success">✅ {{ successMsg }}</div>
    <div v-if="errorMsg"   class="alert alert-error">❌ {{ errorMsg }}</div>

    <form @submit.prevent="handleSubmit" class="form-card">
      <div class="form-group">
        <label>🎬 Judul Film <span class="required">*</span></label>
        <input v-model="form.judul" type="text" placeholder="Contoh: Avengers Endgame" required />
      </div>

      <div class="form-group">
        <label>🎭 Genre <span class="required">*</span></label>
        <select v-model="form.genre_id" required>
          <option value="">-- Pilih Genre --</option>
          <!-- v-for untuk mengisi dropdown dari data API -->
          <option v-for="genre in genres" :key="genre.id" :value="genre.id">
            {{ genre.nama_genre }}
          </option>
        </select>
      </div>

      <div class="form-group">
        <label>🎥 Sutradara <span class="required">*</span></label>
        <input v-model="form.sutradara" type="text" placeholder="Nama Sutradara" required />
      </div>
<!-- Diubah menjadi 3 kolom agar rapi -->
      <div class="form-row">
        <div class="form-group">
          <label>📅 Tanggal Rilis <span class="required">*</span></label>
          <input v-model="form.tanggal_rilis" type="date" required />
        </div>
        <div class="form-group">
          <label>⏱️ Durasi (menit) <span class="required">*</span></label>
          <input v-model="form.durasi" type="number" placeholder="120" min="1" required />
        </div>
        <!-- Tambahan Kolom Rating -->
        <div class="form-group">
          <label>⭐ Rating <span class="required">*</span></label>
          <input 
            v-model="form.rating" 
            type="number" 
            step="0.1" 
            min="0" 
            max="10" 
            placeholder="8.5" 
            required 
          />
        </div>
      </div>
      <div class="form-group">
        <label>🖼️ URL Poster <span class="required">*</span></label>
        <input v-model="form.poster" type="text" placeholder="https://..." required />
        <!-- Preview gambar otomatis saat URL diisi -->
        <img v-if="form.poster" :src="form.poster" alt="Preview Poster" class="poster-preview" />
      </div>

      <div class="form-group">
        <label>🎭 Pilih Aktor <span class="required">*</span></label>
        <div class="checkbox-grid">
          <!-- v-model pada checkbox dengan array: nilai terpilih masuk ke aktor_id[] -->
          <label v-for="aktor in aktors" :key="aktor.id" class="checkbox-item">
            <input type="checkbox" :value="aktor.id" v-model="form.aktor_id" />
            <span>{{ aktor.nama_aktor }}</span>
          </label>
        </div>
        <p class="hint">Pilih minimal 1 aktor</p>
      </div>

      <div class="form-group">
        <label>📖 Sinopsis <span class="required">*</span></label>
        <textarea v-model="form.deskripsi" rows="5" placeholder="Tulis sinopsis film..." required></textarea>
      </div>

      <div class="form-actions">
        <RouterLink to="/kelola-film" class="btn-secondary">Batal</RouterLink>
        <button type="submit" :disabled="loading" class="btn btn-primary">
          <span v-if="loading">⏳ Menyimpan...</span>
          <span v-else>💾 Simpan Film</span>
        </button>
      </div>
    </form>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'
import { RouterLink, useRouter }    from 'vue-router'
import api                          from '../../../utils/api'

const router     = useRouter()
const loading    = ref(false)
const successMsg = ref('')
const errorMsg   = ref('')
const genres     = ref([])
const aktors     = ref([])

// reactive() untuk form dengan banyak field
const form = reactive({
  judul:         '',
  genre_id:      '',
  sutradara:     '',
  tanggal_rilis: '',
  durasi:        '',
  rating:        '',
  poster:        '',
  deskripsi:     '',
  aktor_id:      [],   // Array karena bisa pilih BANYAK aktor
})

onMounted(async () => {
  await ambilDataAwal()
})

// Ambil genre & aktor secara PARALEL (lebih cepat dari satu per satu)
const ambilDataAwal = async () => {
  try {
    // Promise.all: jalankan beberapa request BERSAMAAN
    const [genreRes, aktorRes] = await Promise.all([
      api.get('/genre'),
      api.get('/aktor'),
    ])
    genres.value = genreRes.data.data
    aktors.value = aktorRes.data.data
  } catch (err) {
    errorMsg.value = 'Gagal memuat data genre/aktor!'
    console.error(err)
  }
}

const handleSubmit = async () => {
  if (form.aktor_id.length === 0) {
    errorMsg.value = 'Pilih minimal 1 aktor!'
    return
  }

  try {
    loading.value    = true
    errorMsg.value   = ''
    successMsg.value = ''

    // POST request ke /films (token otomatis dari api.js)
    await api.post('/film', form)

    successMsg.value = 'Film berhasil ditambahkan!'

    router.push('/kelola-film')

    // Reset semua field form ke nilai awal
    Object.assign(form, {
      judul: '', genre_id: '', sutradara: '',
      tanggal_rilis: '', durasi: '', poster: '',
      deskripsi: '', aktor_id: []
    })
    window.scrollTo({ top: 0, behavior: 'smooth' })

  } catch (err) {
    if (err.response?.status === 422) {
      const errors = err.response.data.errors
      errorMsg.value = Object.values(errors)[0][0]
    } else {
      errorMsg.value = err.response?.data?.message || 'Gagal menyimpan film!'
    }
  } finally {
    loading.value = false
  }
}
</script>

<style scoped>
/* Container Main Layout */
.container {
  display: flex;
  flex-direction: column;
  align-items: center;
  width: 100%;
  max-width: 760px;
  margin: 0 auto;
  padding: 32px 16px;
  box-sizing: border-box;
}

/* Page Header */
.page-title {
  width: 100%;
  margin-bottom: 8px;
}

.btn-back {
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
  margin: 12px 0 24px 0;
}

/* Alerts */
.alert {
  width: 100%;
  padding: 12px 16px;
  border-radius: 10px;
  margin-bottom: 20px;
  font-size: 14px;
  box-sizing: border-box;
}

.alert-success {
  background: #d1fae5;
  color: #065f46;
  border: 1px solid #a7f3d0;
}

.alert-error {
  background: #fef2f2;
  color: #dc2626;
  border: 1px solid #fecaca;
}

/* Form Card Wrapper */
.form-card {
  width: 100%;
  background: #ffffff;
  border-radius: 16px;
  padding: 32px;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
  display: flex;
  flex-direction: column;
  gap: 20px;
  box-sizing: border-box;
}

.form-group {
  display: flex;
  flex-direction: column;
  gap: 8px;
  text-align: left;
}

/* Grid 3 Kolom untuk Tanggal, Durasi, dan Rating */
.form-row {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 16px;
}

label {
  font-size: 14px;
  font-weight: 600;
  color: #4a5568;
}

.required {
  color: #e94560;
}

/* Inputs, Selects, & Textarea */
input, select, textarea {
  width: 100%;
  padding: 12px 16px;
  border: 1px solid #cbd5e1;
  border-radius: 10px;
  font-size: 14px;
  font-family: inherit;
  color: #1a1a2e;
  outline: none;
  background-color: #ffffff;
  transition: all 0.2s ease-in-out;
  box-sizing: border-box;
}

input:focus, select:focus, textarea:focus {
  border-color: #e94560;
  box-shadow: 0 0 0 3px rgba(233, 69, 96, 0.15);
}

select {
  cursor: pointer;
}

textarea {
  resize: vertical;
}

/* Poster Preview */
.poster-preview {
  margin-top: 8px;
  width: 120px;
  height: 170px;
  object-fit: cover;
  border-radius: 10px;
  border: 1px solid #cbd5e1;
  box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

/* Checkbox Grid Aktor */
.checkbox-grid {
  display: flex;
  flex-wrap: wrap;
  gap: 10px;
  margin-top: 4px;
}

.checkbox-item {
  display: flex;
  align-items: center;
  gap: 8px;
  background: #f8fafc;
  padding: 8px 16px;
  border: 1px solid #e2e8f0;
  border-radius: 20px;
  cursor: pointer;
  font-size: 13px;
  color: #4a5568;
  font-weight: 500;
  transition: all 0.2s ease;
  user-select: none;
}

.checkbox-item:hover {
  background: #f1f5f9;
  border-color: #cbd5e1;
}

.checkbox-item:has(input:checked) {
  background: #fef2f2;
  border-color: #fecaca;
  color: #e94560;
  font-weight: 600;
}

.checkbox-item input[type="checkbox"] {
  width: auto;
  accent-color: #e94560;
  cursor: pointer;
}

.hint {
  font-size: 12px;
  color: #94a3b8;
  margin-top: 2px;
}

/* Form Action Buttons */
.form-actions {
  display: flex;
  gap: 12px;
  justify-content: flex-end;
  align-items: center;
  padding-top: 16px;
  border-top: 1px solid #f1f5f9;
  margin-top: 8px;
}

.btn {
  padding: 12px 24px;
  border-radius: 10px;
  font-size: 14px;
  font-weight: 600;
  border: none;
  cursor: pointer;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  transition: all 0.2s ease;
}

.btn-primary {
  background: #e94560;
  color: #ffffff;
}

.btn-primary:hover {
  background: #d03750;
}

.btn-primary:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

.btn-secondary {
  background: #f1f5f9;
  color: #475569;
  padding: 12px 24px;
  border-radius: 10px;
  text-decoration: none;
  font-size: 14px;
  font-weight: 600;
  transition: background 0.2s;
}

.btn-secondary:hover {
  background: #e2e8f0;
}

/* Responsive Tablet/Mobile */
@media (max-width: 640px) {
  .form-row {
    grid-template-columns: 1fr;
  }
  
  .form-card {
    padding: 20px;
  }

  .form-actions {
    flex-direction: column-reverse;
  }

  .btn, .btn-secondary {
    width: 100%;
    text-align: center;
    box-sizing: border-box;
  }
}
</style>