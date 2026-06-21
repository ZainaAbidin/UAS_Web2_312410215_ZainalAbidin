<template>
  <div>
    <div class="page-header">
      <div>
        <h1 class="page-title">Penulis / Penerbit</h1>
        <p class="page-subtitle">Kelola data penulis dan penerbit buku/komik</p>
      </div>
      <button @click="openCreate" class="btn-primary">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Tambah
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
          <h3 class="font-semibold text-gray-800">Daftar Penulis/Penerbit</h3>
          <span class="badge-info badge">{{ authors.length }}</span>
        </div>
        <div class="flex items-center gap-2">
          <select v-model="filterType" class="form-select w-36 text-sm">
            <option value="">Semua</option>
            <option value="author">Penulis</option>
            <option value="publisher">Penerbit</option>
          </select>
          <input v-model="search" type="text" placeholder="Cari penulis/penerbit..." class="form-input w-full sm:w-56" />
        </div>
      </div>
      <div class="table-wrapper">
        <table class="table">
          <thead>
            <tr><th>No</th><th>Nama</th><th>Tipe</th><th>Email</th><th>No. Telepon</th><th>Aksi</th></tr>
          </thead>
          <tbody>
            <tr v-if="loading">
              <td colspan="6" class="text-center py-10"><div class="w-8 h-8 border-4 border-primary-500 border-t-transparent rounded-full animate-spin mx-auto"></div></td>
            </tr>
            <tr v-else-if="!filtered.length">
              <td colspan="6" class="text-center text-gray-400 py-10">Tidak ada data</td>
            </tr>
            <tr v-for="(a, idx) in filtered" :key="a.id">
              <td class="text-gray-400">{{ idx + 1 }}</td>
              <td class="font-medium text-gray-900">{{ a.name }}</td>
              <td><StatusBadge :status="a.type" /></td>
              <td class="text-gray-500">{{ a.email || '-' }}</td>
              <td class="text-gray-500">{{ a.phone || '-' }}</td>
              <td>
                <div class="flex gap-2">
                  <button @click="openEdit(a)" class="btn-secondary btn-sm">Edit</button>
                  <button @click="confirmDelete(a)" class="btn-danger btn-sm">Hapus</button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <BaseModal v-if="showModal" :title="isEdit ? 'Edit Penulis/Penerbit' : 'Tambah Penulis/Penerbit'"
      :loading="saving" @close="showModal = false" @confirm="save">
      <div class="space-y-4">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div class="form-group col-span-2">
            <label class="form-label">Nama <span class="text-red-500">*</span></label>
            <input v-model="form.name" type="text" class="form-input" placeholder="Nama lengkap" />
            <p v-if="errors.name" class="form-error">{{ errors.name }}</p>
          </div>
          <div class="form-group">
            <label class="form-label">Tipe <span class="text-red-500">*</span></label>
            <select v-model="form.type" class="form-select">
              <option value="author">Penulis</option>
              <option value="publisher">Penerbit</option>
            </select>
          </div>
          <div class="form-group">
            <label class="form-label">No. Telepon</label>
            <input v-model="form.phone" type="text" class="form-input" placeholder="08xxxxxxxxxx" />
          </div>
          <div class="form-group col-span-2">
            <label class="form-label">Email</label>
            <input v-model="form.email" type="email" class="form-input" placeholder="email@gmail.com" />
          </div>
          <div class="form-group col-span-2">
            <label class="form-label">Bio</label>
            <textarea v-model="form.bio" rows="3" class="form-input" placeholder="Deskripsi singkat..."></textarea>
          </div>
        </div>
      </div>
    </BaseModal>

    <BaseModal v-if="showDelete" title="Konfirmasi Hapus" confirmText="Ya, Hapus"
      :loading="saving" @close="showDelete = false" @confirm="doDelete">
      <div class="text-center py-4">
        <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
          <svg class="w-8 h-8 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
        </div>
        <p class="text-gray-700 font-medium">Hapus <strong>"{{ selected?.name }}"</strong>?</p>
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

const form = reactive({ name: '', type: 'author', bio: '', email: '', phone: '' })

const filtered = computed(() =>
  authors.value.filter(a => {
    const matchSearch = a.name.toLowerCase().includes(search.value.toLowerCase())
    const matchType   = filterType.value ? a.type === filterType.value : true
    return matchSearch && matchType
  })
)

function showAlert(type, message) {
  alert.type = type; alert.message = message; alert.show = true
  setTimeout(() => { alert.show = false }, 3000)
}

async function fetchData() {
  loading.value = true
  const { data } = await api.get('/api/authors')
  authors.value = data.data
  loading.value = false
}

function openCreate() {
  isEdit.value = false
  Object.assign(form, { name: '', type: 'author', bio: '', email: '', phone: '' })
  errors.value = {}; showModal.value = true
}

function openEdit(a) {
  isEdit.value = true; selected.value = a
  Object.assign(form, { name: a.name, type: a.type, bio: a.bio || '', email: a.email || '', phone: a.phone || '' })
  errors.value = {}; showModal.value = true
}

function confirmDelete(a) { selected.value = a; showDelete.value = true }

async function save() {
  errors.value = {}
  if (!form.name) { errors.value.name = 'Nama wajib diisi'; return }
  saving.value = true
  try {
    if (isEdit.value) {
      await api.put(`/api/authors/${selected.value.id}`, form)
      showAlert('success', 'Data berhasil diubah')
    } else {
      await api.post('/api/authors', form)
      showAlert('success', 'Data berhasil ditambahkan')
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
    await api.delete(`/api/authors/${selected.value.id}`)
    showAlert('success', 'Data berhasil dihapus')
    showDelete.value = false; fetchData()
  } catch (e) {
    showAlert('error', 'Gagal menghapus data')
  } finally {
    saving.value = false
  }
}

onMounted(fetchData)
</script>
