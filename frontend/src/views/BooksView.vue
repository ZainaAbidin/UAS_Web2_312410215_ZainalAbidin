<template>
  <div>
    <div class="page-header">
      <div>
        <h1 class="page-title">Manajemen Buku / Komik</h1>
        <p class="page-subtitle">Kelola koleksi buku dan komik digital</p>
      </div>
      <button @click="openCreate" class="btn-primary">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Tambah Buku
      </button>
    </div>

    <transition name="fade">
      <div v-if="alert.show" class="mb-5 px-4 py-3 rounded-xl border text-sm flex items-center gap-2"
        :class="alert.type === 'success' ? 'bg-emerald-50 border-emerald-200 text-emerald-800' : 'bg-red-50 border-red-200 text-red-800'">
        {{ alert.message }}
      </div>
    </transition>

    <div class="card">
      <div class="card-header">
        <div class="flex items-center gap-3">
          <h3 class="font-semibold text-gray-800">Koleksi Buku/Komik</h3>
          <span class="badge-info badge">{{ books.length }}</span>
        </div>
        <div class="flex items-center gap-2">
          <select v-model="filterType" class="form-select w-32 text-sm">
            <option value="">Semua Tipe</option>
            <option value="book">Buku</option>
            <option value="comic">Komik</option>
          </select>
          <input v-model="search" type="text" placeholder="Cari judul, isbn, atau penerbit..." class="form-input w-full sm:w-64" />
        </div>
      </div>
      <div class="table-wrapper">
        <table class="table">
          <thead>
            <tr><th>No</th><th>Judul</th><th>Tipe</th><th>Kategori</th><th>Penulis</th><th>Stok</th><th>Harga Sewa</th><th>Status</th><th>Aksi</th></tr>
          </thead>
          <tbody>
            <tr v-if="loading">
              <td colspan="9" class="text-center py-10"><div class="w-8 h-8 border-4 border-primary-500 border-t-transparent rounded-full animate-spin mx-auto"></div></td>
            </tr>
            <tr v-else-if="!filtered.length">
              <td colspan="9" class="text-center text-gray-400 py-10">Tidak ada data buku</td>
            </tr>
            <tr v-for="(b, idx) in filtered" :key="b.id">
              <td class="text-gray-400">{{ idx + 1 }}</td>
              <td class="font-medium text-gray-900 max-w-[180px] truncate">{{ b.title }}</td>
              <td><StatusBadge :status="b.type" /></td>
              <td class="text-gray-500">{{ b.category_name || '-' }}</td>
              <td class="text-gray-500">{{ b.author_name || '-' }}</td>
              <td>
                <span :class="b.stock <= 0 ? 'badge-danger badge' : 'badge-success badge'">{{ b.stock }}</span>
              </td>
              <td class="text-gray-700">Rp {{ Number(b.rental_price).toLocaleString('id-ID') }}</td>
              <td><StatusBadge :status="b.status" /></td>
              <td>
                <div class="flex gap-2">
                  <button @click="openEdit(b)" class="btn-secondary btn-sm">Edit</button>
                  <button @click="confirmDelete(b)" class="btn-danger btn-sm">Hapus</button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

        <BaseModal v-if="showModal" :title="isEdit ? 'Edit Buku/Komik' : 'Tambah Buku/Komik'"
      :loading="saving" @close="showModal = false" @confirm="save" width="640px">
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div class="form-group col-span-2">
          <label class="form-label">Judul <span class="text-red-500">*</span></label>
          <input v-model="form.title" type="text" class="form-input" placeholder="Judul buku/komik" />
          <p v-if="errors.title" class="form-error">{{ errors.title }}</p>
        </div>
        <div class="form-group">
          <label class="form-label">Tipe <span class="text-red-500">*</span></label>
          <select v-model="form.type" class="form-select">
            <option value="book">Buku</option>
            <option value="comic">Komik</option>
          </select>
        </div>
        <div class="form-group">
          <label class="form-label">ISBN</label>
          <input v-model="form.isbn" type="text" class="form-input" placeholder="978-xxx-xxx-xxx" />
        </div>
        <div class="form-group">
          <label class="form-label">Kategori <span class="text-red-500">*</span></label>
          <select v-model="form.category_id" class="form-select">
            <option value="">-- Pilih Kategori --</option>
            <option v-for="c in categories" :key="c.id" :value="c.id">{{ c.name }}</option>
          </select>
          <p v-if="errors.category_id" class="form-error">{{ errors.category_id }}</p>
        </div>
        <div class="form-group">
          <label class="form-label">Penulis/Penerbit <span class="text-red-500">*</span></label>
          <select v-model="form.author_id" class="form-select">
            <option value="">-- Pilih Penulis --</option>
            <option v-for="a in authors" :key="a.id" :value="a.id">{{ a.name }}</option>
          </select>
          <p v-if="errors.author_id" class="form-error">{{ errors.author_id }}</p>
        </div>
        <div class="form-group">
          <label class="form-label">Stok <span class="text-red-500">*</span></label>
          <input v-model.number="form.stock" type="number" min="0" class="form-input" />
        </div>
        <div class="form-group">
          <label class="form-label">Tahun Terbit</label>
          <input v-model="form.published_year" type="number" class="form-input" placeholder="2024" />
        </div>
        <div class="form-group">
          <label class="form-label">Harga Sewa (Rp) <span class="text-red-500">*</span></label>
          <input v-model.number="form.rental_price" type="number" min="0" class="form-input" />
        </div>
        <div class="form-group">
          <label class="form-label">Status</label>
          <select v-model="form.status" class="form-select">
            <option value="available">Tersedia</option>
            <option value="unavailable">Tidak Tersedia</option>
          </select>
        </div>
        <div class="form-group col-span-2">
          <label class="form-label">Deskripsi</label>
          <textarea v-model="form.description" rows="3" class="form-input" placeholder="Sinopsis buku/komik..."></textarea>
        </div>
      </div>
    </BaseModal>

        <BaseModal v-if="showDelete" title="Konfirmasi Hapus" confirmText="Ya, Hapus"
      :loading="saving" @close="showDelete = false" @confirm="doDelete">
      <div class="text-center py-4">
        <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
          <svg class="w-8 h-8 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
        </div>
        <p class="text-gray-700 font-medium">Hapus buku <strong>"{{ selected?.title }}"</strong>?</p>
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

const books      = ref([])
const categories = ref([])
const authors    = ref([])
const loading    = ref(true)
const saving     = ref(false)
const showModal  = ref(false)
const showDelete = ref(false)
const isEdit     = ref(false)
const selected   = ref(null)
const search     = ref('')
const filterType = ref('')
const errors     = ref({})
const alert      = reactive({ show: false, type: 'success', message: '' })

const form = reactive({
  title: '', type: 'book', category_id: '', author_id: '', isbn: '',
  description: '', stock: 0, published_year: '', rental_price: 0, status: 'available'
})

const filtered = computed(() =>
  books.value.filter(b => {
    const matchSearch = b.title.toLowerCase().includes(search.value.toLowerCase())
    const matchType   = filterType.value ? b.type === filterType.value : true
    return matchSearch && matchType
  })
)

function showAlert(type, message) {
  alert.type = type; alert.message = message; alert.show = true
  setTimeout(() => { alert.show = false }, 3000)
}

async function fetchData() {
  loading.value = true
  const [bRes, cRes, aRes] = await Promise.all([
    api.get('/api/books'), api.get('/api/categories'), api.get('/api/authors')
  ])
  books.value      = bRes.data.data
  categories.value = cRes.data.data
  authors.value    = aRes.data.data
  loading.value    = false
}

function openCreate() {
  isEdit.value = false
  Object.assign(form, { title: '', type: 'book', category_id: '', author_id: '', isbn: '', description: '', stock: 0, published_year: '', rental_price: 0, status: 'available' })
  errors.value = {}; showModal.value = true
}

function openEdit(b) {
  isEdit.value = true; selected.value = b
  Object.assign(form, { title: b.title, type: b.type, category_id: b.category_id, author_id: b.author_id, isbn: b.isbn || '', description: b.description || '', stock: b.stock, published_year: b.published_year || '', rental_price: b.rental_price, status: b.status })
  errors.value = {}; showModal.value = true
}

function confirmDelete(b) { selected.value = b; showDelete.value = true }

async function save() {
  errors.value = {}
  if (!form.title) { errors.value.title = 'Judul wajib diisi'; return }
  if (!form.category_id) { errors.value.category_id = 'Kategori wajib dipilih'; return }
  if (!form.author_id) { errors.value.author_id = 'Penulis wajib dipilih'; return }
  saving.value = true
  try {
    if (isEdit.value) {
      await api.put(`/api/books/${selected.value.id}`, form)
      showAlert('success', 'Buku berhasil diubah')
    } else {
      await api.post('/api/books', form)
      showAlert('success', 'Buku berhasil ditambahkan')
    }
    showModal.value = false; fetchData()
  } catch (e) {
    showAlert('error', e.response?.data?.message || 'Terjadi kesalahan')
  } finally {
    saving.value = false
  }
}

async function doDelete() {
  saving.value = true
  try {
    await api.delete(`/api/books/${selected.value.id}`)
    showAlert('success', 'Buku berhasil dihapus')
    showDelete.value = false; fetchData()
  } catch (e) {
    showAlert('error', 'Gagal menghapus buku')
  } finally {
    saving.value = false
  }
}

onMounted(fetchData)
</script>
