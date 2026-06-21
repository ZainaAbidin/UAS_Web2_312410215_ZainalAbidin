<template>
  <div>
    <div class="page-header">
      <div>
        <h1 class="page-title">Manajemen Anggota</h1>
        <p class="page-subtitle">Kelola data anggota perpustakaan digital</p>
      </div>
      <button @click="openCreate" class="btn-primary">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Tambah Anggota
      </button>
    </div>

    <transition name="fade">
      <div v-if="alert.show" class="mb-5 px-4 py-3 rounded-xl border text-sm"
        :class="alert.type === 'success' ? 'bg-emerald-50 border-emerald-200 text-emerald-800' : 'bg-red-50 border-red-200 text-red-800'">
        {{ alert.message }}
      </div>
    </transition>

    <div class="card">
      <div class="card-header">
        <div class="flex items-center gap-3">
          <h3 class="font-semibold text-gray-800">Daftar Anggota</h3>
          <span class="badge-info badge">{{ members.length }}</span>
        </div>
        <div class="flex items-center gap-2">
          <select v-model="filterStatus" class="form-select w-32 text-sm">
            <option value="">Semua</option>
            <option value="active">Aktif</option>
            <option value="inactive">Nonaktif</option>
          </select>
          <input v-model="search" type="text" placeholder="Cari nama / kode..." class="form-input w-full sm:w-48" />
        </div>
      </div>
      <div class="table-wrapper">
        <table class="table">
          <thead>
            <tr><th>No</th><th>Kode</th><th>Nama</th><th>Email</th><th>Telepon</th><th>Jenis Kelamin</th><th>Status</th><th>Aksi</th></tr>
          </thead>
          <tbody>
            <tr v-if="loading">
              <td colspan="8" class="text-center py-10"><div class="w-8 h-8 border-4 border-primary-500 border-t-transparent rounded-full animate-spin mx-auto"></div></td>
            </tr>
            <tr v-else-if="!filtered.length">
              <td colspan="8" class="text-center text-gray-400 py-10">Tidak ada data anggota</td>
            </tr>
            <tr v-for="(m, idx) in filtered" :key="m.id">
              <td class="text-gray-400">{{ idx + 1 }}</td>
              <td><span class="font-mono text-xs bg-gray-100 px-2 py-1 rounded">{{ m.member_code }}</span></td>
              <td class="font-medium text-gray-900">{{ m.name }}</td>
              <td class="text-gray-500">{{ m.email || '-' }}</td>
              <td class="text-gray-500">{{ m.phone || '-' }}</td>
              <td class="text-gray-500">
                <div v-if="m.gender === 'male'" class="flex items-center gap-1.5 text-blue-500">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                  <span class="text-gray-500">Laki-laki</span>
                </div>
                <div v-else-if="m.gender === 'female'" class="flex items-center gap-1.5 text-pink-500">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                  <span class="text-gray-500">Perempuan</span>
                </div>
                <span v-else>-</span>
              </td>
              <td><StatusBadge :status="m.status" /></td>
              <td>
                <div class="flex gap-2">
                  <button @click="openEdit(m)" class="btn-secondary btn-sm">Edit</button>
                  <button @click="confirmDelete(m)" class="btn-danger btn-sm">Hapus</button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <BaseModal v-if="showModal" :title="isEdit ? 'Edit Anggota' : 'Tambah Anggota'"
      :loading="saving" @close="showModal = false" @confirm="save" width="600px">
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div class="form-group col-span-2">
          <label class="form-label">Nama Lengkap <span class="text-red-500">*</span></label>
          <input v-model="form.name" type="text" class="form-input" placeholder="Nama lengkap anggota" />
          <p v-if="errors.name" class="form-error">{{ errors.name }}</p>
        </div>
        <div class="form-group">
          <label class="form-label">Email</label>
          <input v-model="form.email" type="email" class="form-input" placeholder="email@gmail.com" />
        </div>
        <div class="form-group">
          <label class="form-label">No. Telepon</label>
          <input v-model="form.phone" type="text" class="form-input" placeholder="08xxxxxxxxxx" />
        </div>
        <div class="form-group">
          <label class="form-label">Jenis Kelamin</label>
          <select v-model="form.gender" class="form-select">
            <option value="">-- Pilih --</option>
            <option value="male">Laki-laki</option>
            <option value="female">Perempuan</option>
          </select>
        </div>
        <div class="form-group">
          <label class="form-label">Tanggal Lahir</label>
          <input v-model="form.date_of_birth" type="date" class="form-input" />
        </div>
        <div class="form-group">
          <label class="form-label">Status</label>
          <select v-model="form.status" class="form-select">
            <option value="active">Aktif</option>
            <option value="inactive">Nonaktif</option>
          </select>
        </div>
        <div class="form-group col-span-2">
          <label class="form-label">Alamat</label>
          <textarea v-model="form.address" rows="2" class="form-input" placeholder="Alamat lengkap..."></textarea>
        </div>
      </div>
    </BaseModal>

    <BaseModal v-if="showDelete" title="Konfirmasi Hapus" confirmText="Ya, Hapus"
      :loading="saving" @close="showDelete = false" @confirm="doDelete">
      <div class="text-center py-4">
        <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
          <svg class="w-8 h-8 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7a4 4 0 11-8 0 4 4 0 018 0zM9 14a6 6 0 00-6 6v1h12v-1a6 6 0 00-6-6zM21 12h-6"/></svg>
        </div>
        <p class="text-gray-700 font-medium">Hapus anggota <strong>"{{ selected?.name }}"</strong>?</p>
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

const members      = ref([])
const loading      = ref(true)
const saving       = ref(false)
const showModal    = ref(false)
const showDelete   = ref(false)
const isEdit       = ref(false)
const selected     = ref(null)
const search       = ref('')
const filterStatus = ref('')
const errors       = ref({})
const alert        = reactive({ show: false, type: 'success', message: '' })

const form = reactive({ name: '', email: '', phone: '', address: '', gender: '', date_of_birth: '', status: 'active' })

const filtered = computed(() =>
  members.value.filter(m => {
    const q = search.value.toLowerCase()
    const matchSearch = m.name.toLowerCase().includes(q) || m.member_code.toLowerCase().includes(q)
    const matchStatus = filterStatus.value ? m.status === filterStatus.value : true
    return matchSearch && matchStatus
  })
)

function showAlert(type, message) {
  alert.type = type; alert.message = message; alert.show = true
  setTimeout(() => { alert.show = false }, 3000)
}

async function fetchData() {
  loading.value = true
  const { data } = await api.get('/api/members')
  members.value = data.data; loading.value = false
}

function openCreate() {
  isEdit.value = false
  Object.assign(form, { name: '', email: '', phone: '', address: '', gender: '', date_of_birth: '', status: 'active' })
  errors.value = {}; showModal.value = true
}

function openEdit(m) {
  isEdit.value = true; selected.value = m
  Object.assign(form, { name: m.name, email: m.email || '', phone: m.phone || '', address: m.address || '', gender: m.gender || '', date_of_birth: m.date_of_birth || '', status: m.status })
  errors.value = {}; showModal.value = true
}

function confirmDelete(m) { selected.value = m; showDelete.value = true }

async function save() {
  errors.value = {}
  if (!form.name) { errors.value.name = 'Nama wajib diisi'; return }
  saving.value = true
  try {
    if (isEdit.value) {
      await api.put(`/api/members/${selected.value.id}`, form)
      showAlert('success', 'Anggota berhasil diubah')
    } else {
      await api.post('/api/members', form)
      showAlert('success', 'Anggota berhasil ditambahkan')
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
    await api.delete(`/api/members/${selected.value.id}`)
    showAlert('success', 'Anggota berhasil dihapus')
    showDelete.value = false; fetchData()
  } catch (e) {
    showAlert('error', 'Gagal menghapus anggota')
  } finally {
    saving.value = false
  }
}

onMounted(fetchData)
</script>
