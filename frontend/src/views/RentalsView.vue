<template>
  <div>
    <div class="page-header">
      <div>
        <h1 class="page-title">Manajemen Peminjaman</h1>
        <p class="page-subtitle">Kelola status peminjaman buku dan komik</p>
      </div>
      <button @click="openCreate" class="btn-primary">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Tambah Peminjaman
      </button>
    </div>

    <transition name="fade">
      <div v-if="alert.show" class="mb-5 px-4 py-3 rounded-xl border text-sm"
        :class="alert.type === 'success' ? 'bg-emerald-50 border-emerald-200 text-emerald-800' : 'bg-red-50 border-red-200 text-red-800'">
        {{ alert.message }}
      </div>
    </transition>

        <div class="flex border-b border-gray-200 mb-6">
      <button @click="currentTab = 'list'" class="px-6 py-3 font-medium text-sm border-b-2 transition-colors" :class="currentTab === 'list' ? 'border-[#5c7a52] text-[#2e3d2c]' : 'border-transparent text-gray-500 hover:text-gray-700'">Daftar Peminjaman</button>
      <button @click="currentTab = 'fines'" class="px-6 py-3 font-medium text-sm border-b-2 transition-colors" :class="currentTab === 'fines' ? 'border-[#5c7a52] text-[#2e3d2c]' : 'border-transparent text-gray-500 hover:text-gray-700'">Laporan Denda</button>
    </div>

        <div v-if="currentTab === 'list'">
            <div class="flex items-center gap-2 mb-5 overflow-x-auto pb-1">
      <button v-for="tab in statusTabs" :key="tab.value" @click="filterStatus = tab.value"
        class="px-4 py-2 rounded-xl text-sm font-medium whitespace-nowrap transition-all"
        :class="filterStatus === tab.value ? 'bg-primary-600 text-white shadow-sm' : 'bg-white text-gray-600 border border-gray-200 hover:bg-gray-50'">
        {{ tab.label }}
        <span class="ml-1.5 text-xs px-1.5 py-0.5 rounded-full"
          :class="filterStatus === tab.value ? 'bg-white/20 text-white' : 'bg-gray-100 text-gray-500'">
          {{ countByStatus(tab.value) }}
        </span>
      </button>
    </div>

    <div class="card">
      <div class="card-header">
        <h3 class="font-semibold text-gray-800">Daftar Peminjaman</h3>
        <input v-model="search" type="text" placeholder="Cari anggota / buku..." class="form-input w-full sm:w-48" />
      </div>
      <div class="table-wrapper">
        <table class="table">
          <thead>
            <tr><th>No</th><th>Kode</th><th>Anggota</th><th>Buku</th><th>Tgl Pinjam</th><th>Tgl Kembali</th><th>Status</th><th>Denda</th><th>Aksi</th></tr>
          </thead>
          <tbody>
            <tr v-if="loading">
              <td colspan="9" class="text-center py-10"><div class="w-8 h-8 border-4 border-primary-500 border-t-transparent rounded-full animate-spin mx-auto"></div></td>
            </tr>
            <tr v-else-if="!filtered.length">
              <td colspan="9" class="text-center text-gray-400 py-10">Tidak ada data peminjaman</td>
            </tr>
            <tr v-for="(r, idx) in filtered" :key="r.id">
              <td class="text-gray-400">{{ idx + 1 }}</td>
              <td><span class="font-mono text-xs bg-gray-100 px-2 py-1 rounded">{{ r.rental_code }}</span></td>
              <td>
                <div>
                  <p class="font-medium text-gray-900 text-sm">{{ r.member_name }}</p>
                  <p class="text-xs text-gray-400">{{ r.member_code }}</p>
                </div>
              </td>
              <td class="max-w-[150px] truncate">
                <p class="text-sm text-gray-700">{{ r.book_title }}</p>
                <StatusBadge :status="r.book_type" />
              </td>
              <td class="text-gray-500 text-sm">{{ r.rent_date }}</td>
              <td class="text-sm" :class="isOverdue(r) ? 'text-red-500 font-medium' : 'text-gray-500'">{{ r.due_date }}</td>
              <td><StatusBadge :status="r.status" /></td>
              <td class="text-sm" :class="getFine(r) > 0 ? 'text-red-600 font-semibold' : 'text-gray-400'">
                {{ getFine(r) > 0 ? 'Rp ' + getFine(r).toLocaleString('id-ID') : '-' }}
              </td>
              <td>
                <div class="flex gap-1.5">
                  <button @click="openStatusModal(r)" class="btn-secondary btn-sm">Status</button>
                  <button @click="confirmDelete(r)" class="btn-danger btn-sm">Hapus</button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
    </div> 
        <div v-if="currentTab === 'fines'">
      <div class="grid grid-cols-2 gap-4 mb-6">
                <div class="card p-6 border-l-4 border-amber-500 flex items-center justify-between">
          <div>
            <p class="text-sm font-medium text-gray-500 mb-1">Estimasi Denda (Berjalan)</p>
            <h3 class="text-2xl font-bold text-amber-600">Rp {{ estimatedFines.toLocaleString('id-ID') }}</h3>
            <p class="text-xs text-gray-400 mt-1">Dari buku yang sedang terlambat</p>
          </div>
          <div class="w-12 h-12 bg-amber-50 rounded-full flex items-center justify-center">
            <svg class="w-6 h-6 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
          </div>
        </div>
                <div class="card p-6 border-l-4 border-[#5c7a52] flex items-center justify-between">
          <div>
            <p class="text-sm font-medium text-gray-500 mb-1">Total Denda Masuk</p>
            <h3 class="text-2xl font-bold text-[#5c7a52]">Rp {{ collectedFines.toLocaleString('id-ID') }}</h3>
            <p class="text-xs text-gray-400 mt-1">Dari peminjaman yang sudah dikembalikan</p>
          </div>
          <div class="w-12 h-12 bg-[#5c7a52]/10 rounded-full flex items-center justify-center">
            <svg class="w-6 h-6 text-[#5c7a52]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
          </div>
        </div>
      </div>

      <div class="card">
        <div class="card-header">
          <h3 class="font-semibold text-gray-800">Riwayat & Status Denda</h3>
        </div>
        <div class="table-wrapper">
          <table class="table">
            <thead>
              <tr><th>Kode</th><th>Anggota</th><th>Tgl Pinjam</th><th>Jatuh Tempo</th><th>Tgl Kembali</th><th>Status</th><th>Nominal Denda</th></tr>
            </thead>
            <tbody>
              <tr v-if="loading">
                <td colspan="7" class="text-center py-10"><div class="w-8 h-8 border-4 border-primary-500 border-t-transparent rounded-full animate-spin mx-auto"></div></td>
              </tr>
              <tr v-else-if="!fineHistory.length">
                <td colspan="7" class="text-center text-gray-400 py-10">Belum ada data denda</td>
              </tr>
              <tr v-for="r in fineHistory" :key="r.id">
                <td><span class="font-mono text-xs bg-gray-100 px-2 py-1 rounded">{{ r.rental_code }}</span></td>
                <td>
                  <div>
                    <p class="font-medium text-gray-900 text-sm">{{ r.member_name }}</p>
                    <p class="text-xs text-gray-400">{{ r.book_title }}</p>
                  </div>
                </td>
                <td class="text-gray-500 text-sm">{{ r.rent_date }}</td>
                <td class="text-gray-500 text-sm">{{ r.due_date }}</td>
                <td class="text-gray-500 text-sm">{{ r.return_date || '-' }}</td>
                <td>
                  <span v-if="r.status === 'returned'" class="inline-flex items-center gap-1 text-xs text-[#5c7a52] bg-[#5c7a52]/10 px-2 py-1 rounded-md font-medium">Lunas</span>
                  <span v-else class="inline-flex items-center gap-1 text-xs text-amber-600 bg-amber-50 px-2 py-1 rounded-md font-medium">Belum Dibayar</span>
                </td>
                <td class="text-sm font-semibold text-red-600">Rp {{ getFine(r).toLocaleString('id-ID') }}</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div> 
        <BaseModal v-if="showModal" title="Tambah Peminjaman"
      :loading="saving" @close="showModal = false" @confirm="save" width="580px">
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div class="form-group col-span-2">
          <label class="form-label">Anggota <span class="text-red-500">*</span></label>
          <select v-model="form.member_id" class="form-select">
            <option value="">-- Pilih Anggota --</option>
            <option v-for="m in members" :key="m.id" :value="m.id">{{ m.member_code }} - {{ m.name }}</option>
          </select>
          <p v-if="errors.member_id" class="form-error">{{ errors.member_id }}</p>
        </div>
        <div class="form-group col-span-2">
          <label class="form-label">Buku / Komik <span class="text-red-500">*</span></label>
          <select v-model="form.book_id" class="form-select">
            <option value="">-- Pilih Buku --</option>
            <option v-for="b in availableBooks" :key="b.id" :value="b.id">{{ b.title }} (Stok: {{ b.stock }})</option>
          </select>
          <p v-if="errors.book_id" class="form-error">{{ errors.book_id }}</p>
        </div>
        <div class="form-group">
          <label class="form-label">Tanggal Pinjam <span class="text-red-500">*</span></label>
          <input v-model="form.rent_date" type="date" class="form-input" />
        </div>
        <div class="form-group">
          <label class="form-label">Tanggal Kembali <span class="text-red-500">*</span></label>
          <input v-model="form.due_date" type="date" class="form-input" />
        </div>
        <div class="form-group col-span-2">
          <label class="form-label">Catatan</label>
          <textarea v-model="form.notes" rows="2" class="form-input" placeholder="Catatan tambahan..."></textarea>
        </div>
      </div>
    </BaseModal>

        <BaseModal v-if="showStatusModal" title="Ubah Status Peminjaman" confirmText="Update Status"
      :loading="saving" @close="showStatusModal = false" @confirm="updateStatus">
      <div class="space-y-4">
        <div class="p-4 bg-gray-50 rounded-xl">
          <p class="text-sm text-gray-600">Kode: <strong>{{ selected?.rental_code }}</strong></p>
          <p class="text-sm text-gray-600">Buku: <strong>{{ selected?.book_title }}</strong></p>
          <p class="text-sm text-gray-600">Anggota: <strong>{{ selected?.member_name }}</strong></p>
          <p class="text-sm text-gray-600 mt-1">Status Saat Ini: <StatusBadge :status="selected?.status" /></p>
        </div>
        <div class="form-group">
          <label class="form-label">Status Baru <span class="text-red-500">*</span></label>
          <select v-model="newStatus" class="form-select">
            <option value="pending">Menunggu</option>
            <option value="active">Aktif (Dipinjam)</option>
            <option value="returned">Dikembalikan</option>
            <option value="overdue">Terlambat</option>
          </select>
        </div>
        <div v-if="newStatus === 'returned'" class="p-3 bg-amber-50 border border-amber-200 rounded-xl text-sm text-amber-800 flex items-start gap-2">
          <svg class="w-5 h-5 text-amber-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
          <span>Mengubah status ke "Dikembalikan" akan menghitung denda keterlambatan secara otomatis (Rp 1.000/hari).</span>
        </div>
      </div>
    </BaseModal>

        <BaseModal v-if="showDelete" title="Konfirmasi Hapus" confirmText="Ya, Hapus"
      :loading="saving" @close="showDelete = false" @confirm="doDelete">
      <div class="text-center py-4">
        <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
          <svg class="w-8 h-8 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
        </div>
        <p class="text-gray-700 font-medium">Hapus peminjaman <strong>"{{ selected?.rental_code }}"</strong>?</p>
        <p class="text-gray-400 text-sm mt-1">Tindakan ini tidak dapat dibatalkan.</p>
      </div>
    </BaseModal>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, reactive } from 'vue'
import api from '../api/axios'
import BaseModal from '../components/BaseModal.vue'
import StatusBadge from '../components/StatusBadge.vue'

const rentals         = ref([])
const members         = ref([])
const books           = ref([])
const loading         = ref(true)
const saving          = ref(false)
const showModal       = ref(false)
const showStatusModal = ref(false)
const showDelete      = ref(false)
const selected        = ref(null)
const search          = ref('')
const filterStatus    = ref('')
const currentTab      = ref('list')
const newStatus       = ref('active')
const errors          = ref({})
const alert           = reactive({ show: false, type: 'success', message: '' })

const today = new Date().toISOString().split('T')[0]
const form = reactive({ member_id: '', book_id: '', rent_date: today, due_date: '', notes: '' })

const statusTabs = [
  { value: '', label: 'Semua' },
  { value: 'pending', label: 'Menunggu' },
  { value: 'active', label: 'Aktif' },
  { value: 'returned', label: 'Dikembalikan' },
  { value: 'overdue', label: 'Terlambat' },
]

const availableBooks = computed(() => books.value.filter(b => b.stock > 0))

const filtered = computed(() => {
  const q = search.value.toLowerCase()
  return rentals.value.filter(r => {
    const matchStatus = filterStatus.value ? r.status === filterStatus.value : true
    const matchSearch = !q || r.rental_code.toLowerCase().includes(q)
      || (r.member_name || '').toLowerCase().includes(q)
      || (r.book_title || '').toLowerCase().includes(q)
    return matchStatus && matchSearch
  })
})

const estimatedFines = computed(() => {
  return rentals.value.filter(r => r.status !== 'returned').reduce((sum, r) => sum + getFine(r), 0)
})

const collectedFines = computed(() => {
  return rentals.value.filter(r => r.status === 'returned').reduce((sum, r) => sum + (Number(r.fine) || 0), 0)
})

const fineHistory = computed(() => {
  return rentals.value.filter(r => getFine(r) > 0).sort((a, b) => new Date(b.created_at || 0) - new Date(a.created_at || 0))
})

function countByStatus(status) {
  return status ? rentals.value.filter(r => r.status === status).length : rentals.value.length
}

function isOverdue(r) {
  const due = new Date(r.due_date)
  const today = new Date()
  due.setHours(0,0,0,0)
  today.setHours(0,0,0,0)
  return r.status !== 'returned' && today > due
}

function getFine(r) {
  if (r.status === 'returned') return Number(r.fine) || 0

  const due = new Date(r.due_date)
  const today = new Date()
  due.setHours(0,0,0,0)
  today.setHours(0,0,0,0)

  if (today > due && r.status !== 'returned') {
    const diffDays = Math.ceil((today - due) / (1000 * 60 * 60 * 24))
    return diffDays * 1000
  }
  return 0
}

function showAlert(type, message) {
  alert.type = type; alert.message = message; alert.show = true
  setTimeout(() => { alert.show = false }, 3000)
}

async function fetchData() {
  loading.value = true
  const [rRes, mRes, bRes] = await Promise.all([
    api.get('/api/rentals'), api.get('/api/members'), api.get('/api/books')
  ])
  rentals.value = rRes.data.data; members.value = mRes.data.data; books.value = bRes.data.data
  loading.value = false
}

function openCreate() {
  const dueDate = new Date(); dueDate.setDate(dueDate.getDate() + 7)
  Object.assign(form, { member_id: '', book_id: '', rent_date: today, due_date: dueDate.toISOString().split('T')[0], notes: '' })
  errors.value = {}; showModal.value = true
}

function openStatusModal(r) { selected.value = r; newStatus.value = r.status; showStatusModal.value = true }
function confirmDelete(r) { selected.value = r; showDelete.value = true }

async function save() {
  errors.value = {}
  if (!form.member_id) { errors.value.member_id = 'Anggota wajib dipilih'; return }
  if (!form.book_id) { errors.value.book_id = 'Buku wajib dipilih'; return }
  saving.value = true
  try {
    await api.post('/api/rentals', form)
    showAlert('success', 'Peminjaman berhasil dibuat')
    showModal.value = false; fetchData()
  } catch (e) {
    showAlert('error', e.response?.data?.message || 'Terjadi kesalahan')
  } finally {
    saving.value = false
  }
}

async function updateStatus() {
  saving.value = true
  try {
    await api.put(`/api/rentals/${selected.value.id}/status`, { status: newStatus.value })
    showAlert('success', `Status berhasil diubah menjadi ${newStatus.value}`)
    showStatusModal.value = false; fetchData()
  } catch (e) {
    showAlert('error', e.response?.data?.message || 'Terjadi kesalahan')
  } finally {
    saving.value = false
  }
}

async function doDelete() {
  saving.value = true
  try {
    await api.delete(`/api/rentals/${selected.value.id}`)
    showAlert('success', 'Peminjaman berhasil dihapus')
    showDelete.value = false; fetchData()
  } catch (e) {
    showAlert('error', 'Gagal menghapus peminjaman')
  } finally {
    saving.value = false
  }
}

onMounted(fetchData)
</script>
