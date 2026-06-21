<template>
  <div>
        <div class="page-header">
      <div>
        <h1 class="page-title">Dashboard</h1>
        <p class="page-subtitle">Selamat datang, {{ auth.user?.name }}! Berikut ringkasan sistem E-Library.</p>
      </div>
    </div>

        <div v-if="loading" class="flex justify-center items-center py-20">
      <div class="w-10 h-10 border-4 border-primary-500 border-t-transparent rounded-full animate-spin"></div>
    </div>

    <template v-else>
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
        <div v-for="stat in stats" :key="stat.label" class="stat-card border-none bg-white shadow-sm rounded-2xl p-5 flex items-center gap-4">
          <div class="w-14 h-14 rounded-2xl flex items-center justify-center flex-shrink-0" :class="stat.color">
            <span v-html="stat.icon" class="text-dark"></span>
          </div>
          <div>
            <p class="text-3xl font-bold text-dark">{{ stat.value }}</p>
            <p class="text-xs font-medium text-gray-500 mt-0.5 uppercase tracking-wide">{{ stat.label }}</p>
          </div>
        </div>
      </div>

            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <div v-for="s in rentalStatuses" :key="s.label" class="bg-stone/60 rounded-2xl p-4 flex flex-col justify-center">
          <p class="text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-1">{{ s.label }}</p>
          <p class="text-2xl font-bold" :class="s.textClass">{{ s.value }}</p>
        </div>
      </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="card">
          <div class="card-header">
            <h3 class="font-semibold text-gray-800">Peminjaman Terbaru</h3>
            <router-link to="/rentals" class="text-xs text-primary-600 hover:underline">Lihat semua</router-link>
          </div>
          <div class="overflow-x-auto">
            <table class="table">
              <thead>
                <tr>
                  <th>Anggota</th>
                  <th>Buku</th>
                  <th>Status</th>
                </tr>
              </thead>
              <tbody>
                <tr v-if="!recentRentals.length">
                  <td colspan="3" class="text-center text-gray-400 py-6">Belum ada data</td>
                </tr>
                <tr v-for="r in recentRentals" :key="r.id">
                  <td class="font-medium">{{ r.member_name }}</td>
                  <td class="text-gray-500 max-w-[140px] truncate">{{ r.book_title }}</td>
                  <td><StatusBadge :status="r.status" /></td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

                <div class="card">
          <div class="card-header">
            <h3 class="font-semibold text-gray-800">Buku Terpopuler</h3>
          </div>
          <div class="card-body">
            <div v-if="!topBooks.length" class="text-center text-gray-400 py-6">Belum ada data</div>
            <div v-for="(book, idx) in topBooks" :key="idx" class="flex items-center gap-3 py-2.5 border-b border-gray-100 last:border-0">
              <span class="w-7 h-7 rounded-lg bg-primary-100 text-primary-700 flex items-center justify-center text-xs font-bold flex-shrink-0">{{ idx + 1 }}</span>
              <div class="flex-1 min-w-0">
                <p class="text-sm font-medium text-gray-800 truncate">{{ book.title }}</p>
                <div class="flex items-center gap-1 text-xs text-gray-400">
                  <svg v-if="book.type === 'comic'" class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                  <svg v-else class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                  <span>{{ book.type === 'comic' ? 'Komik' : 'Buku' }}</span>
                </div>
              </div>
              <span class="text-sm font-semibold text-primary-600">{{ book.rental_count }}×</span>
            </div>
          </div>
        </div>
      </div>
    </template>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import { useAuthStore } from '../stores/auth'
import api from '../api/axios'
import StatusBadge from '../components/StatusBadge.vue'

const auth         = useAuthStore()
const loading      = ref(true)
const data         = ref(null)

const recentRentals = computed(() => data.value?.recent_rentals || [])
const topBooks      = computed(() => data.value?.top_books || [])

const stats = computed(() => {
  if (!data.value) return []
  const s = data.value.stats
  return [
    { label: 'Total Buku / Komik', value: s.total_books,    color: 'bg-stone', icon: bookIcon },
    { label: 'Total Anggota',      value: s.total_members,  color: 'bg-stone', icon: memberIcon },
    { label: 'Total Peminjaman',   value: s.total_rentals,  color: 'bg-stone', icon: rentalIcon },
    { label: 'Peminjaman Aktif',   value: s.active_rentals, color: 'bg-stone', icon: activeIcon },
  ]
})

const rentalStatuses = computed(() => {
  if (!data.value) return []
  const s = data.value.stats
  return [
    { label: 'Menunggu',     value: s.pending_rentals,  textClass: 'text-dark' },
    { label: 'Aktif',        value: s.active_rentals,   textClass: 'text-dark' },
    { label: 'Dikembalikan', value: s.returned_rentals, textClass: 'text-dark' },
    { label: 'Terlambat',    value: s.overdue_rentals,  textClass: 'text-ochre' },
  ]
})

onMounted(async () => {
  try {
    const res = await api.get('/api/dashboard')
    data.value = res.data.data
  } catch (e) {
    console.error(e)
  } finally {
    loading.value = false
  }
})

const bookIcon   = `<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>`
const memberIcon = `<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>`
const rentalIcon = `<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>`
const activeIcon = `<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>`
</script>
