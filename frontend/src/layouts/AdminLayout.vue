<template>
  <div class="flex h-screen bg-parchment">
        <div v-if="sidebarOpen" class="fixed inset-0 bg-dark/20 backdrop-blur-sm z-30 lg:hidden" @click="sidebarOpen = false"></div>

        <aside class="sidebar lg:translate-x-0" :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'">
            <div class="px-6 py-5 border-b border-white/10">
        <div class="flex items-center gap-3">
          <div class="w-9 h-9 bg-[#5c7a52] rounded-xl flex items-center justify-center">
            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
            </svg>
          </div>
          <div>
            <h1 class="text-white font-bold text-sm leading-tight">E-Library</h1>
            <p class="text-slate-400 text-xs">Peminjaman Buku & Komik</p>
          </div>
        </div>
      </div>

            <nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto">
        <p class="px-3 text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Menu Utama</p>

        <router-link v-for="item in navItems" :key="item.to" :to="item.to"
          class="sidebar-link" :class="{ active: $route.path === item.to }">
          <span v-html="item.icon" class="w-5 h-5 flex-shrink-0"></span>
          <span>{{ item.label }}</span>
        </router-link>
      </nav>

            <div class="px-4 py-4 border-t border-white/10">
        <div class="flex items-center gap-3 mb-3">
          <div class="w-8 h-8 bg-[#5c7a52] rounded-full flex items-center justify-center flex-shrink-0">
            <span class="text-white text-xs font-bold">{{ initials }}</span>
          </div>
          <div class="min-w-0">
            <p class="text-white text-sm font-medium truncate">{{ auth.user?.name }}</p>
            <p class="text-slate-400 text-xs truncate">Administrator</p>
          </div>
        </div>
        <button @click="handleLogout"
          class="w-full flex items-center gap-2 px-3 py-2 rounded-lg text-sm text-red-400 hover:bg-red-500/10 hover:text-red-300 transition-all">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
          </svg>
          Logout
        </button>
      </div>
    </aside>

        <main class="page-container flex-1 overflow-y-auto w-full" :class="sidebarOpen ? 'lg:ml-64' : 'ml-0'">
            <header class="sticky top-0 z-30 bg-parchment/80 backdrop-blur border-b border-stone px-6 py-3 flex items-center justify-between">
        <div class="flex items-center gap-4">
          <button @click="sidebarOpen = !sidebarOpen" class="p-2 -ml-2 rounded-lg hover:bg-stone/50 text-dark transition-colors lg:hidden" title="Toggle Sidebar">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
               <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
          </button>
          <h2 class="text-lg font-bold text-dark hidden sm:block">{{ currentDate }}</h2>
        </div>
        <div class="flex items-center gap-2">
          <span class="inline-flex items-center gap-2 text-sm font-semibold text-dark bg-[#FCFAF5] px-3 py-1.5 rounded-lg border border-stone shadow-sm">
            <svg class="w-4 h-4 text-[#5c7a52]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            {{ currentTime }}
          </span>
        </div>
      </header>

            <div class="p-6">
        <router-view />
      </div>
    </main>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../stores/auth'

const auth   = useAuthStore()
const router = useRouter()

const sidebarOpen = ref(window.innerWidth >= 1024)

router.afterEach(() => {
  if (window.innerWidth < 1024) {
    sidebarOpen.value = false
  }
})
const currentTime = ref('')
let timer = null

const initials = computed(() => {
  if (!auth.user?.name) return 'A'
  return auth.user.name.split(' ').map(n => n[0]).join('').substring(0, 2).toUpperCase()
})

const currentDate = computed(() => {
  return new Date().toLocaleDateString('id-ID', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' })
})

const updateTime = () => {
  currentTime.value = new Date().toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' })
}

onMounted(() => {
  updateTime()
  timer = setInterval(updateTime, 1000)
})

onUnmounted(() => {
  if (timer) clearInterval(timer)
})

async function handleLogout() {
  await auth.logout()
  router.push('/login')
}

const navItems = [
  {
    to: '/dashboard',
    label: 'Dashboard',
    icon: `<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>`,
  },
  {
    to: '/books',
    label: 'Buku / Komik',
    icon: `<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>`,
  },
  {
    to: '/categories',
    label: 'Kategori',
    icon: `<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A2 2 0 013 12V7a4 4 0 014-4z"/></svg>`,
  },
  {
    to: '/authors',
    label: 'Penulis / Penerbit',
    icon: `<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>`,
  },
  {
    to: '/members',
    label: 'Anggota',
    icon: `<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>`,
  },
  {
    to: '/rentals',
    label: 'Peminjaman',
    icon: `<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>`,
  },
]
</script>
