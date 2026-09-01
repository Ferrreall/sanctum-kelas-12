<template>
  <div class="container">
    <!-- Header dengan Tombol Kembali yang Rapi & Ke Tengah -->
    <div class="header-container">
      <RouterLink to="/kelola-film" class="btn-back">
        <span class="arrow">←</span> Batal Edit
      </RouterLink>
      <h1 class="page-title">✏️ Edit Film</h1>
    </div>

    <div v-if="successMsg" class="alert alert-success">✅ {{ successMsg }}</div>
    <div v-if="errorMsg"   class="alert alert-error">❌ {{ errorMsg }}</div>

    <div v-if="loadingData" class="loading-text">⏳ Memuat data film...</div>

    <form v-else @submit.prevent="handleUpdate" class="form-card">
      <div class="form-group">
        <label>🎬 Judul Film <span class="required">*</span></label>
        <input v-model="form.judul" type="text" placeholder="Masukkan judul film" required />
      </div>

      <div class="form-group">
        <label>🎭 Genre <span class="required">*</span></label>
        <select v-model="form.genre_id" required>
          <option value="">-- Pilih Genre --</option>
          <option v-for="genre in genres" :key="genre.id" :value="genre.id">
            {{ genre.nama_genre }}
          </option>
        </select>
      </div>

      <div class="form-group">
        <label>🎥 Sutradara <span class="required">*</span></label>
        <input v-model="form.sutradara" type="text" placeholder="Nama sutradara" required />
      </div>

      <!-- Grid 3 Kolom untuk Tanggal, Durasi, dan Rating -->
      <div class="form-row">
        <div class="form-group">
          <label>📅 Tanggal Rilis <span class="required">*</span></label>
          <input v-model="form.tanggal_rilis" type="date" required />
        </div>
        <div class="form-group">
          <label>⏱️ Durasi (menit) <span class="required">*</span></label>
          <input v-model="form.durasi" type="number" placeholder="120" min="1" required />
        </div>
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
        <img v-if="form.poster" :src="form.poster" alt="Preview Poster" class="poster-preview" />
      </div>

      <div class="form-group">
        <label>🎭 Pilih Aktor <span class="required">*</span></label>
        <div class="checkbox-grid">
          <label 
            v-for="aktor in aktors" 
            :key="aktor.id" 
            :for="'aktor-' + aktor.id" 
            class="checkbox-item"
          >
            <input 
              type="checkbox" 
              :id="'aktor-' + aktor.id"
              :value="aktor.id" 
              v-model="form.aktor_id" 
            />
            <span>{{ aktor.nama_aktor }}</span>
          </label>
        </div>
      </div>

      <div class="form-group">
        <label>📖 Sinopsis <span class="required">*</span></label>
        <textarea v-model="form.deskripsi" rows="5" placeholder="Tulis sinopsis..." required></textarea>
      </div>

      <div class="form-actions">
        <RouterLink to="/kelola-film" class="btn-secondary">Batal</RouterLink>
        <button type="submit" :disabled="loadingSubmit" class="btn btn-primary">
          <span v-if="loadingSubmit">⏳ Mengupdate...</span>
          <span v-else>💾 Update Film</span>
        </button>
      </div>
    </form>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted }           from 'vue'
import { RouterLink, useRouter, useRoute }    from 'vue-router'
import api                                    from '../../../utils/api'

const router = useRouter()
const route  = useRoute()
const filmId = route.params.id

const loadingData   = ref(true)
const loadingSubmit = ref(false)
const successMsg    = ref('')
const errorMsg      = ref('')
const genres        = ref([])
const aktors        = ref([])

const form = reactive({
  judul: '', 
  genre_id: '', 
  sutradara: '',
  tanggal_rilis: '', 
  durasi: '', 
  rating: '', // <-- Sudah ditambahkan
  poster: '',
  deskripsi: '', 
  aktor_id: [],
})

onMounted(async () => {
  await ambilDataFilm()
})

const ambilDataFilm = async () => {
  try {
    loadingData.value = true

    const [filmRes, genreRes, aktorRes] = await Promise.all([
      api.get(`/film/${filmId}`),
      api.get('/genre'),
      api.get('/aktor')
    ])

    genres.value = genreRes.data.data
    aktors.value = aktorRes.data.data

    const filmData = filmRes.data.data

    form.judul         = filmData.judul
    form.genre_id      = filmData.genre_id
    form.sutradara     = filmData.sutradara
    form.tanggal_rilis = filmData.tanggal_rilis
    form.durasi        = filmData.durasi
    form.rating        = filmData.rating
    form.poster        = filmData.poster
    form.deskripsi     = filmData.deskripsi

    // Tambahkan Optional Chaining (?.) & Fallback Array ([])
    form.aktor_id = (filmData.aktor || filmData.aktors || []).map(a => a.id || a)

  } catch (err) {
    console.error('Error detail saat ambil data:', err) // <-- INI PENTING untuk me-log error JS
    errorMsg.value = 'Gagal memuat data film untuk diedit.'
  } finally {
    loadingData.value = false
  }
}

const handleUpdate = async () => {
  if (form.aktor_id.length === 0) {
    errorMsg.value = 'Pilih minimal 1 aktor!'
    return
  }
  try {
    loadingSubmit.value = true
    errorMsg.value      = ''

    await api.put(`/film/${filmId}`, form)

    successMsg.value = 'Data film berhasil diupdate!'
    router.push('/kelola-film') // <-- Redirect ke halaman kelola film setelah update sukses
    window.scrollTo({ top: 0, behavior: 'smooth' })

  } catch (err) {
    errorMsg.value = 'Gagal mengupdate film!'
  } finally {
    loadingSubmit.value = false
  }
}
</script>

<style scoped>
/* Container & Alignment */
.container {
  display: flex;
  flex-direction: column;
  align-items: center;
  width: 100%;
  padding: 32px 16px;
  box-sizing: border-box;
}

/* Header & Tombol Kembali */
.header-container {
  max-width: 720px;
  width: 100%;
  margin-bottom: 20px;
  display: flex;
  flex-direction: column;
  align-items: flex-start;
  gap: 12px;
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

.btn-back .arrow {
  transition: transform 0.2s ease;
}

.btn-back:hover {
  background-color: #f8fafc;
  color: #e94560;
  border-color: #cbd5e1;
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.08);
}

.btn-back:hover .arrow {
  transform: translateX(-3px);
}

.page-title {
  font-size: 26px;
  font-weight: 700;
  color: #1a1a2e;
  margin: 0;
}

/* Card Form */
.form-card {
  background: white;
  border-radius: 16px;
  padding: 32px;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
  display: flex;
  flex-direction: column;
  gap: 20px;
  max-width: 720px;
  width: 100%;
  box-sizing: border-box;
}

.form-group {
  display: flex;
  flex-direction: column;
  gap: 6px;
}

.form-row {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 16px;
}

@media (max-width: 600px) {
  .form-row {
    grid-template-columns: 1fr;
  }
}

label {
  font-size: 13px;
  font-weight: 600;
  color: #333;
}

.required {
  color: #e94560;
  margin-left: 2px;
}

input, select, textarea {
  padding: 11px 14px;
  border: 2px solid #e0e0e0;
  border-radius: 10px;
  font-size: 14px;
  font-family: inherit;
  outline: none;
  transition: border-color 0.2s;
  width: 100%;
  box-sizing: border-box;
}

input:focus, select:focus, textarea:focus {
  border-color: #e94560;
  box-shadow: 0 0 0 3px rgba(233, 69, 96, 0.1);
}

.poster-preview {
  margin-top: 10px;
  width: 120px;
  height: 160px;
  object-fit: cover;
  border-radius: 8px;
  border: 2px solid #e0e0e0;
}

/* Checkbox Aktor */
.checkbox-grid {
  display: flex;
  flex-wrap: wrap;
  gap: 8px;
  margin-top: 4px;
}

.checkbox-item {
  display: flex;
  align-items: center;
  gap: 6px;
  background: #f4f4f8;
  padding: 6px 14px;
  border-radius: 20px;
  cursor: pointer;
  font-size: 13px;
  font-weight: normal;
  transition: background 0.2s;
  user-select: none;
}

.checkbox-item:has(input:checked) {
  background: #fee2e2;
  color: #e94560;
  font-weight: 600;
}

/* Buttons Action */
.form-actions {
  display: flex;
  gap: 12px;
  justify-content: flex-end;
  padding-top: 16px;
  border-top: 1px solid #f0f0f0;
}

.btn {
  padding: 10px 24px;
  border-radius: 10px;
  font-size: 14px;
  font-weight: 600;
  border: none;
  cursor: pointer;
  transition: background 0.2s;
}

.btn-primary {
  background: #e94560;
  color: white;
}

.btn-primary:hover {
  background: #d03750;
}

.btn-primary:disabled {
  background: #ccc;
  cursor: not-allowed;
}

.btn-secondary {
  background: #f0f0f0;
  color: #555;
  padding: 10px 24px;
  border-radius: 10px;
  text-decoration: none;
  font-size: 14px;
  font-weight: 600;
  display: inline-flex;
  align-items: center;
}

.btn-secondary:hover {
  background: #e4e4e4;
}

/* Alert & Messages */
.alert {
  max-width: 720px;
  width: 100%;
  padding: 12px 16px;
  border-radius: 10px;
  margin-bottom: 16px;
  font-size: 14px;
  box-sizing: border-box;
}

.alert-success {
  background: #d1fae5;
  color: #065f46;
}

.alert-error {
  background: #fee2e2;
  color: #991b1b;
}

.loading-text {
  font-size: 16px;
  color: #666;
  margin-top: 40px;
}
</style>