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
.container {
  display: flex;
  flex-direction: column;
  align-items: center; /* Memusatkan semua elemen di dalam container secara horizontal */
  justify-content: center;
  width: 100%;
  min-height: 100vh; /* (Opsional) jika ingin tepat di tengah secara vertikal juga */
  padding: 24px;
}
.page-title { margin-bottom: 24px; }
.page-title h1 { font-size: 28px; color: #1a1a2e; margin-top: 12px; }
.btn-back { color: #666; font-size: 14px; }
.btn-back:hover { color: #e94560; text-decoration: none; }

.form-card { background: white; border-radius: 16px; padding: 32px; box-shadow: 0 4px 20px rgba(0,0,0,0.08); display: flex; flex-direction: column; gap: 20px; max-width: 720px; }
.form-group { display: flex; flex-direction: column; gap: 6px; }
.form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
label { font-size: 13px; font-weight: 600; color: #333; }
.required { color: #e94560; margin-left: 2px; }
input, select, textarea { padding: 11px 14px; border: 2px solid #e0e0e0; border-radius: 10px; font-size: 14px; font-family: inherit; outline: none; transition: border-color 0.2s; }
input:focus, select:focus, textarea:focus { border-color: #e94560; box-shadow: 0 0 0 3px rgba(233,69,96,0.1); }
.poster-preview { margin-top: 10px; width: 120px; height: 160px; object-fit: cover; border-radius: 8px; border: 2px solid #e0e0e0; }
.checkbox-grid { display: flex; flex-wrap: wrap; gap: 8px; margin-top: 4px; }
.checkbox-item { display: flex; align-items: center; gap: 6px; background: #f4f4f8; padding: 6px 14px; border-radius: 20px; cursor: pointer; font-size: 13px; font-weight: normal; transition: background 0.2s; }
.checkbox-item:has(input:checked) { background: #fee2e2; color: #e94560; font-weight: 600; }
.hint { font-size: 12px; color: #999; margin-top: 4px; }
.form-actions { display: flex; gap: 12px; justify-content: flex-end; padding-top: 8px; border-top: 1px solid #f0f0f0; }
.btn-secondary { background: #f0f0f0; color: #555; padding: 10px 24px; border-radius: 10px; text-decoration: none; font-size: 14px; font-weight: 600; }
</style>