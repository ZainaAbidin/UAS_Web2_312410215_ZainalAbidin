<template>
  <div class="relative min-h-screen bg-[#2E3D2C] text-white flex flex-col justify-between overflow-hidden">
        <canvas ref="bgCanvas" class="absolute inset-0 z-0 pointer-events-none opacity-40"></canvas>

        <header class="relative z-10 w-full max-w-7xl mx-auto px-6 py-6 flex justify-between items-center">
      <div class="flex items-center gap-3">
        <div class="w-10 h-10 bg-[#5c7a52] rounded-xl flex items-center justify-center shadow-lg border border-white/20">
          <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
          </svg>
        </div>
        <div>
          <h1 class="text-lg font-bold tracking-wide">E-Library</h1>
          <p class="text-xs text-stone/70">Peminjaman Buku & Komik</p>
        </div>
      </div>

      <div>
        <router-link
          v-if="auth.isAuthenticated"
          to="/dashboard"
          class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-[#5c7a52] text-white font-semibold text-sm border border-white/10 hover:bg-[#465e3e] shadow-lg hover:shadow-xl hover:-translate-y-0.5 transition-all duration-300"
        >
          Masuk Dashboard
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
          </svg>
        </router-link>
        <router-link
          v-else
          to="/login"
          class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-white text-[#2E3D2C] font-semibold text-sm hover:bg-stone hover:shadow-xl hover:-translate-y-0.5 transition-all duration-300"
        >
          Login Admin
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
          </svg>
        </router-link>
      </div>
    </header>

        <main class="relative z-10 w-full max-w-7xl mx-auto px-6 py-12 flex-1 flex flex-col lg:flex-row items-center justify-between gap-12">
            <div class="flex-1 space-y-6 text-center lg:text-left">
        <h2 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold tracking-tight leading-tight">
          Sistem Informasi <br class="hidden sm:inline" />
          <span class="inline-block mt-3 text-transparent bg-clip-text bg-gradient-to-r from-[#e1e9df] via-[#A8BC9A] to-[#E8E2D9]">
            Rental Buku & Komik
          </span>
        </h2>
        <p class="text-stone/80 text-base sm:text-lg max-w-xl mx-auto lg:mx-0">
          Selamat datang di platform E-Library. Nikmati kemudahan dalam mengelola sirkulasi buku, komik digital, manajemen kategori genre, status peminjaman, data denda, hingga histori secara efisien!
        </p>

                <div class="flex flex-wrap items-center justify-center lg:justify-start gap-4">
          <router-link
            v-if="auth.isAuthenticated"
            to="/dashboard"
            class="px-8 py-3.5 rounded-xl bg-[#5c7a52] text-white font-bold hover:bg-[#465e3e] shadow-lg hover:shadow-[#5c7a52]/30 hover:scale-105 transition-all duration-300"
          >
            Akses Dashboard Admin
          </router-link>
          <router-link
            v-else
            to="/login"
            class="px-8 py-3.5 rounded-xl bg-white text-[#2E3D2C] font-bold hover:bg-stone shadow-lg hover:shadow-white/15 hover:scale-105 transition-all duration-300"
          >
            Mulai Sekarang
          </router-link>
        </div>
      </div>

            <div class="w-full lg:w-5/12 max-w-lg">
        <div class="bg-white/5 backdrop-blur-xl border border-white/10 rounded-3xl p-8 shadow-2xl relative">
                    <div class="absolute -top-10 -left-10 w-40 h-40 bg-emerald-500/10 rounded-full blur-3xl pointer-events-none"></div>
          <div class="absolute -bottom-10 -right-10 w-40 h-40 bg-orange-500/10 rounded-full blur-3xl pointer-events-none"></div>

          <h3 class="text-xl font-bold mb-6 text-center border-b border-white/10 pb-4 tracking-wide text-stone">
            Statistik Perpustakaan Saat Ini
          </h3>

          <div v-if="loading" class="flex flex-col items-center justify-center py-12 space-y-4">
            <svg class="animate-spin h-8 w-8 text-[#A8BC9A]" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <p class="text-sm text-stone/50">Memuat statistik...</p>
          </div>

          <div v-else class="grid grid-cols-2 gap-6">
                        <div class="bg-white/5 hover:bg-white/10 border border-white/10 rounded-2xl p-5 text-center transition-all duration-300 hover:scale-105 group">
              <div class="w-10 h-10 mx-auto mb-3 bg-[#5c7a52]/20 text-[#A8BC9A] rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                </svg>
              </div>
              <p class="text-3xl font-extrabold text-[#E8E2D9]">{{ stats.total_books }}</p>
              <p class="text-xs text-stone/60 font-medium uppercase tracking-wider mt-1">Total Buku</p>
            </div>

                        <div class="bg-white/5 hover:bg-white/10 border border-white/10 rounded-2xl p-5 text-center transition-all duration-300 hover:scale-105 group">
              <div class="w-10 h-10 mx-auto mb-3 bg-[#5c7a52]/20 text-[#A8BC9A] rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A2 2 0 013 12V7a4 4 0 014-4z" />
                </svg>
              </div>
              <p class="text-3xl font-extrabold text-[#E8E2D9]">{{ stats.total_categories }}</p>
              <p class="text-xs text-stone/60 font-medium uppercase tracking-wider mt-1">Genre Kategori</p>
            </div>

                        <div class="bg-white/5 hover:bg-white/10 border border-white/10 rounded-2xl p-5 text-center transition-all duration-300 hover:scale-105 group">
              <div class="w-10 h-10 mx-auto mb-3 bg-[#5c7a52]/20 text-[#A8BC9A] rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
              </div>
              <p class="text-3xl font-extrabold text-[#E8E2D9]">{{ stats.total_authors }}</p>
              <p class="text-xs text-stone/60 font-medium uppercase tracking-wider mt-1">Penulis/Penerbit</p>
            </div>

                        <div class="bg-white/5 hover:bg-white/10 border border-white/10 rounded-2xl p-5 text-center transition-all duration-300 hover:scale-105 group">
              <div class="w-10 h-10 mx-auto mb-3 bg-[#5c7a52]/20 text-[#A8BC9A] rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
              </div>
              <p class="text-3xl font-extrabold text-[#E8E2D9]">{{ stats.total_members }}</p>
              <p class="text-xs text-stone/60 font-medium uppercase tracking-wider mt-1">Anggota Aktif</p>
            </div>
          </div>
        </div>
      </div>
    </main>

        <footer class="relative z-10 w-full max-w-7xl mx-auto px-6 py-6 border-t border-white/10 flex flex-col sm:flex-row justify-between items-center gap-4 text-xs text-stone/50 text-center sm:text-left">
      <p></p>
      <p class="text-stone/40">Ujian Akhir Semester (UAS) Web 2 - E-Library</p>
    </footer>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue'
import { useAuthStore } from '../stores/auth'
import api from '../api/axios'

const auth = useAuthStore()

const stats = ref({
  total_books: 0,
  total_categories: 0,
  total_authors: 0,
  total_members: 0,
})

const loading = ref(true)

const bgCanvas = ref(null)
let ctx = null
let animationFrameId = null
const particles = []

const resizeCanvas = () => {
  if (bgCanvas.value) {
    bgCanvas.value.width = window.innerWidth
    bgCanvas.value.height = window.innerHeight
  }
}

const initParticles = () => {
  const count = Math.min(Math.floor(window.innerWidth / 15), 60)
  particles.length = 0
  for (let i = 0; i < count; i++) {
    particles.push({
      x: Math.random() * window.innerWidth,
      y: Math.random() * window.innerHeight,
      vx: (Math.random() - 0.5) * 0.25,
      vy: (Math.random() - 0.5) * 0.25,
      r: Math.random() * 2 + 1,
    })
  }
}

const animate = () => {
  if (!ctx || !bgCanvas.value) return
  ctx.clearRect(0, 0, bgCanvas.value.width, bgCanvas.value.height)

  const w = bgCanvas.value.width
  const h = bgCanvas.value.height

  particles.forEach(p => {
    p.x += p.vx
    p.y += p.vy

    if (p.x < 0) p.x = w
    if (p.x > w) p.x = 0
    if (p.y < 0) p.y = h
    if (p.y > h) p.y = 0

    ctx.beginPath()
    ctx.arc(p.x, p.y, p.r, 0, Math.PI * 2)
    ctx.fillStyle = '#E8E2D9'
    ctx.fill()
  })

  for (let i = 0; i < particles.length; i++) {
    for (let j = i + 1; j < particles.length; j++) {
      const dx = particles[i].x - particles[j].x
      const dy = particles[i].y - particles[j].y
      const dist = Math.sqrt(dx * dx + dy * dy)

      if (dist < 120) {
        ctx.beginPath()
        ctx.moveTo(particles[i].x, particles[i].y)
        ctx.lineTo(particles[j].x, particles[j].y)
        ctx.strokeStyle = `rgba(168, 188, 154, ${0.15 * (1 - dist / 120)})`
        ctx.lineWidth = 0.5
        ctx.stroke()
      }
    }
  }

  animationFrameId = requestAnimationFrame(animate)
}

const fetchStats = async () => {
  try {
    const { data } = await api.get('/api/public/stats')
    if (data.status) {
      stats.value = data.data
    }
  } catch (error) {
    console.error('Gagal mengambil data statistik publik:', error)
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  fetchStats()

  ctx = bgCanvas.value.getContext('2d')
  resizeCanvas()
  initParticles()
  animate()

  window.addEventListener('resize', resizeCanvas)
})

onUnmounted(() => {
  if (animationFrameId) {
    cancelAnimationFrame(animationFrameId)
  }
  window.removeEventListener('resize', resizeCanvas)
})
</script>

<style scoped>
/* Glassmorphism style helper */
.backdrop-blur-xl {
  backdrop-filter: blur(24px);
  -webkit-backdrop-filter: blur(24px);
}
</style>
