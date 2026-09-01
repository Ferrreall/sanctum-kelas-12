<template>
  <div class="container">
    <div class="page-title">
      <RouterLink to="/dashboard" class="btn-back">← Dashboard</RouterLink>
      <div class="title-row">
        <h1>🌟 Kelola Aktor</h1>
        <RouterLink to="/tambah-aktor" class="btn btn-primary">➕ Tambah Aktor</RouterLink>
      </div>
    </div>

    <div v-if="successMsg" class="alert alert-success">✅ {{ successMsg }}</div>
    <p v-if="loading" class="loading-text">⏳ Memuat data aktor...</p>

    <div v-else class="table-wrapper">
      <table class="film-table">
        <thead>
          <tr>
            <th>No</th>
            <th>Foto</th>
            <th>Nama Aktor</th>
            <th>Gender</th>
            <th>Tgl Lahir</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          <tr v-if="aktors.length === 0">
            <td colspan="6" class="empty-row">Belum ada data aktor.</td>
          </tr>
          <tr v-for="(aktor, index) in aktors" :key="aktor.id">
            <td>{{ index + 1 }}</td>
            <td>
              <img 
                :src="aktor.foto || 'https://via.placeholder.com/50'" 
                :alt="aktor.nama_aktor" 
                class="table-poster" 
              />
            </td>
            <td class="film-title-cell">{{ aktor.nama_aktor }}</td>
            <td>{{ aktor.jenis_kelamin === 'Laki-laki' ? 'Laki-laki' : 'Perempuan' }}</td>
            <td>{{ aktor.tanggal_lahir }}</td>
            <td>
              <div class="action-btns">
                <RouterLink :to="'/edit-aktor/' + aktor.id" class="btn-action btn-edit">✏️ Edit</RouterLink>
                <button 
                  @click="hapusAktor(aktor.id, aktor.nama_aktor)"
                  :disabled="deletingId === aktor.id" 
                  class="btn-action btn-delete"
                >
                  <span v-if="deletingId === aktor.id">⏳</span>
                  <span v-else>🗑️ Hapus</span>
                </button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Modal Popup Konfirmasi -->
    <div v-if="showModal" class="modal-overlay" @click.self="showModal = false">
      <div class="modal-box">
        <h3>⚠️ Konfirmasi Hapus</h3>
        <p>Yakin menghapus aktor: <strong>{{ aktorToDelete?.nama_aktor }}</strong>?</p>
        <p class="modal-warning">Tindakan ini tidak bisa dibatalkan!</p>
        <div class="modal-actions">
          <button @click="showModal = false" class="btn-modal-cancel">Batal</button>
          <button @click="konfirmasiHapus" class="btn-modal-delete">🗑️ Hapus</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted }        from 'vue'
import { RouterLink, useRouter } from 'vue-router'
import api                       from '../../../utils/api'

const router       = useRouter()
const aktors       = ref([])
const loading      = ref(true)
const successMsg   = ref('')
const deletingId   = ref(null)
const showModal    = ref(false)
const aktorToDelete = ref(null)

onMounted(async () => { await ambilAktor() })

const ambilAktor = async () => {
  try {
    loading.value = true
    const res = await api.get('/aktor')
    aktors.value = res.data.data
  } catch (err) { console.error(err) }
  finally { loading.value = false }
}

const hapusAktor = (id, nama_aktor) => {
  aktorToDelete.value = { id, nama_aktor }
  showModal.value     = true
}

const konfirmasiHapus = async () => {
  const id = aktorToDelete.value.id
  showModal.value = false
  try {
    deletingId.value = id
    await api.delete(`/aktor/${id}`)
    aktors.value = aktors.value.filter(a => a.id !== id)
    successMsg.value = `Aktor "${aktorToDelete.value.nama_aktor}" berhasil dihapus!`
    setTimeout(() => { successMsg.value = '' }, 3000)
  } catch (err) { alert('Gagal menghapus aktor!') }
  finally { deletingId.value = null; aktorToDelete.value = null }
}
</script>

<style scoped>
/* Layout & Container Utama */
.container {
  display: flex;
  flex-direction: column;
  align-items: center;
  width: 100%;
  max-width: 1000px;
  margin: 0 auto;
  padding: 32px 16px;
  box-sizing: border-box;
}

/* Page Header & Perbaikan Gap Button Back */
.page-title {
  width: 100%;
  display: flex;
  flex-direction: column;
  align-items: flex-start;
  gap: 12px;
  margin-bottom: 24px;
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
  margin-bottom: 0;
}

.btn-back:hover {
  background-color: #f8fafc;
  color: #e94560;
  border-color: #cbd5e1;
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.08);
  transform: translateX(-3px);
}

.title-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  width: 100%;
}

.title-row h1 {
  font-size: 26px;
  font-weight: 700;
  color: #1a1a2e;
  margin: 0;
}

/* Image/Poster Styling */
.table-poster {
  width: 44px;
  height: 44px;
  object-fit: cover;
  border: 1px solid #cbd5e1;
  display: block;
}

/* Alert & Loading Text */
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

.loading-text {
  font-size: 16px;
  color: #64748b;
  margin-top: 30px;
}

/* Style Tabel Data */
.table-wrapper {
  width: 100%;
  background: white;
  border-radius: 16px;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
  overflow: hidden;
}

.film-table {
  width: 100%;
  border-collapse: collapse;
  text-align: left;
}

.film-table th {
  background: #1a1a2e;
  color: white;
  padding: 16px;
  font-size: 14px;
  font-weight: 600;
}

.film-table td {
  padding: 12px 16px;
  border-bottom: 1px solid #f1f5f9;
  font-size: 14px;
  color: #334155;
  vertical-align: middle;
}

.film-table tbody tr:hover {
  background-color: #f8fafc;
}

.film-table tr:last-child td {
  border-bottom: none;
}

.film-title-cell {
  font-weight: 600;
  color: #1a1a2e;
}

.empty-row {
  text-align: center;
  color: #94a3b8;
  padding: 32px !important;
}

/* Action & Standard Buttons */
.action-btns {
  display: flex;
  gap: 8px;
}

.btn {
  padding: 10px 18px;
  border-radius: 10px;
  font-size: 14px;
  font-weight: 600;
  text-decoration: none;
  border: none;
  cursor: pointer;
  display: inline-flex;
  align-items: center;
  gap: 6px;
  transition: all 0.2s ease;
}

.btn-primary {
  background: #e94560;
  color: white;
}

.btn-primary:hover {
  background: #d03750;
}

.btn-action {
  padding: 6px 12px;
  border-radius: 8px;
  font-size: 13px;
  font-weight: 600;
  text-decoration: none;
  border: none;
  cursor: pointer;
  display: inline-flex;
  align-items: center;
  transition: all 0.2s;
}

.btn-edit {
  background: #eff6ff;
  color: #2563eb;
}

.btn-edit:hover {
  background: #dbeafe;
}

.btn-delete {
  background: #fef2f2;
  color: #dc2626;
}

.btn-delete:hover {
  background: #fee2e2;
}

.btn-delete:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

/* Modal Popup Konfirmasi */
.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100vw;
  height: 100vh;
  background: rgba(0, 0, 0, 0.4);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 999;
}

.modal-box {
  background: white;
  border-radius: 16px;
  padding: 24px;
  width: 90%;
  max-width: 400px;
  box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
  text-align: center;
}

.modal-box h3 {
  margin-top: 0;
  color: #1a1a2e;
}

.modal-warning {
  font-size: 12px;
  color: #dc2626;
  font-weight: 600;
  margin-top: 4px;
}

.modal-actions {
  display: flex;
  gap: 12px;
  justify-content: center;
  margin-top: 20px;
}

.btn-modal-cancel {
  background: #f1f5f9;
  color: #475569;
  border: none;
  padding: 8px 18px;
  border-radius: 8px;
  font-weight: 600;
  cursor: pointer;
  transition: background 0.2s;
}

.btn-modal-cancel:hover {
  background: #e2e8f0;
}

.btn-modal-delete {
  background: #dc2626;
  color: white;
  border: none;
  padding: 8px 18px;
  border-radius: 8px;
  font-weight: 600;
  cursor: pointer;
  transition: background 0.2s;
}

.btn-modal-delete:hover {
  background: #b91c1c;
}
</style>