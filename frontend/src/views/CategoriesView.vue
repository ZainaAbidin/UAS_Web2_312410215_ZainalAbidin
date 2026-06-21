<template>
  <div>
        <div class="page-header">
      <div>
        <h1 class="page-title">Manajemen Kategori</h1>
        <p class="page-subtitle">Kelola genre/kategori buku dan komik</p>
      </div>
      <button @click="openCreate" class="btn-primary">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Tambah Kategori
      </button>
    </div>

        <transition name="fade">
      <div v-if="alert.show" class="mb-5" :class="alert.type === 'success' ? 'alert-success' : 'alert-error'" style="display:flex;align-items:center;gap:8px;padding:12px 16px;border-radius:10px;">
        <span>{{ alert.message }}</span>
      </div>
    </transition>

        <div class="card">
      <div class="card-header">
        <h3 class="font-semibold text-gray-800">Daftar Kategori <span class="badge-info badge ml-2">{{ categories.length }}</span></h3>
        <input v-model="search" type="text" placeholder="Cari kategori..." class="form-input w-full sm:w-56" />
      </div>
      <div class="table-wrapper">
        <table class="table">
          <thead>
            <tr>
              <th>No</th>
              <th>Nama Kategori</th>
              <th>Deskripsi</th>
              <th>Jumlah Buku</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody>
            <tr v-if="loading">
              <td colspan="5" class="text-center py-10"><div class="w-8 h-8 border-4 border-primary-500 border-t-transparent rounded-full animate-spin mx-auto"></div></td>
            </tr>
            <tr v-else-if="!filtered.length">
              <td colspan="5" class="text-center text-gray-400 py-10">Tidak ada data kategori</td>
            </tr>
            <tr v-for="(cat, idx) in filtered" :key="cat.id">
              <td class="text-gray-400">{{ idx + 1 }}</td>
              <td class="font-medium text-gray-900">{{ cat.name }}</td>
              <td class="text-gray-500 max-w-xs truncate">{{ cat.description || '-' }}</td>
              <td><span class="badge-info badge">{{ cat.book_count ?? 0 }} buku</span></td>
              <td>
                <div class="flex items-center gap-2">
                  <button @click="openEdit(cat)" class="btn-secondary btn-sm">Edit</button>
                  <button @click="confirmDelete(cat)" class="btn-danger btn-sm">Hapus</button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

        <BaseModal v-if="showModal" :title="isEdit ? 'Edit Kategori' : 'Tambah Kategori'"
      :loading="saving" @close="showModal = false" @confirm="save">
      <div class="space-y-4">
        <div class="form-group">
          <label class="form-label">Nama Kategori <span class="text-red-500">*</span></label>
          <input v-model="form.name" type="text" class="form-input" placeholder="Contoh: Action, Romance..." />
          <p v-if="errors.name" class="form-error">{{ errors.name }}</p>
        </div>
        <div class="form-group">
          <label class="form-label">Deskripsi</label>
          <textarea v-model="form.description" rows="3" class="form-input" placeholder="Deskripsi kategori..."></textarea>
        </div>
      </div>
    </BaseModal>

        <BaseModal v-if="showDelete" title="Konfirmasi Hapus" confirmText="Ya, Hapus"
      :loading="saving" @close="showDelete = false" @confirm="doDelete">
      <div class="text-center py-4">
        <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
          <svg class="w-8 h-8 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
        </div>
        <p class="text-gray-700 font-medium">Hapus kategori <strong>"{{ selected?.name }}"</strong>?</p>
        <p class="text-gray-400 text-sm mt-1">Tindakan ini tidak dapat dibatalkan.</p>
      </div>
    </BaseModal>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, reactive } from 'vue'
import api from '../api/axios'
import BaseModal from '../components/BaseModal.vue'

const categories = ref([])
const loading    = ref(true)
const saving     = ref(false)
const showModal  = ref(false)
const showDelete = ref(false)
const isEdit     = ref(false)
const selected   = ref(null)
const search     = ref('')
const errors     = ref({})
const alert      = reactive({ show: false, type: 'success', message: '' })

const form = reactive({ name: '', description: '' })

const filtered = computed(() =>
  categories.value.filter(c =>
    c.name.toLowerCase().includes(search.value.toLowerCase())
  )
)

function showAlert(type, message) {
  alert.type = type; alert.message = message; alert.show = true
  setTimeout(() => { alert.show = false }, 3000)
}

async function fetchData() {
  loading.value = true
  const { data } = await api.get('/api/categories')
  categories.value = data.data
  loading.value = false
}

function openCreate() {
  isEdit.value = false; form.name = ''; form.description = ''; errors.value = {}
  showModal.value = true
}

function openEdit(cat) {
  isEdit.value = true; selected.value = cat
  form.name = cat.name; form.description = cat.description || ''
  errors.value = {}; showModal.value = true
}

function confirmDelete(cat) { selected.value = cat; showDelete.value = true }

async function save() {
  errors.value = {}
  if (!form.name) { errors.value.name = 'Nama kategori wajib diisi'; return }
  saving.value = true
  try {
    if (isEdit.value) {
      await api.put(`/api/categories/${selected.value.id}`, form)
      showAlert('success', 'Kategori berhasil diubah')
    } else {
      await api.post('/api/categories', form)
      showAlert('success', 'Kategori berhasil ditambahkan')
    }
    showModal.value = false
    fetchData()
  } catch (e) {
    showAlert('error', e.response?.data?.message || 'Terjadi kesalahan')
  } finally {
    saving.value = false
  }
}

async function doDelete() {
  saving.value = true
  try {
    await api.delete(`/api/categories/${selected.value.id}`)
    showAlert('success', 'Kategori berhasil dihapus')
    showDelete.value = false
    fetchData()
  } catch (e) {
    showAlert('error', 'Gagal menghapus kategori')
  } finally {
    saving.value = false
  }
}

onMounted(fetchData)
</script>
