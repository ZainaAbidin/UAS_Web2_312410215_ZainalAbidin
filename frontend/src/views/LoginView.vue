<template>
  <div class="relative min-h-screen bg-[#2E3D2C] flex items-center justify-center p-4 overflow-hidden">
        <canvas ref="bgCanvas" class="absolute inset-0 w-full h-full pointer-events-none opacity-40"></canvas>

    <div class="relative z-10 w-full max-w-md">
            <div class="bg-[#2E3D2C] border border-[#5c7a52]/30 rounded-3xl p-8 shadow-2xl backdrop-blur-sm">
        <div class="text-center mb-8">
          <h1 class="text-3xl font-extrabold text-white tracking-tight">Welcome!</h1>
        </div>

                <transition name="fade">
          <div v-if="error" class="mb-4 px-4 py-3 rounded-xl bg-red-500/20 border border-red-500/30 text-red-300 text-sm flex items-center gap-2">
            <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
            </svg>
            {{ error }}
          </div>
        </transition>

                <form @submit.prevent="handleLogin" class="space-y-5">
          <div>
            <label class="block text-sm font-medium text-gray-200 mb-2">Email</label>
            <input v-model="form.email" type="email" placeholder="admin@gmail.com" required
              class="w-full bg-white/10 border border-white/20 rounded-xl px-4 py-3 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#A8BC9A] focus:border-[#A8BC9A] transition-all" />
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-200 mb-2">Password</label>
            <div class="relative">
              <input v-model="form.password" :type="showPwd ? 'text' : 'password'" placeholder="••••••••" required
                class="w-full bg-white/10 border border-white/20 rounded-xl px-4 py-3 pr-12 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#A8BC9A] focus:border-[#A8BC9A] transition-all" />
              <button type="button" @click="showPwd = !showPwd"
                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-white transition-colors">
                <svg v-if="!showPwd" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                </svg>
                <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                </svg>
              </button>
            </div>
          </div>

          <button type="submit" :disabled="loading"
            class="w-full bg-[#5c7a52] hover:bg-[#465e3e] text-white font-semibold py-3 px-6 rounded-xl transition-all duration-200 flex items-center justify-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed">
            <svg v-if="loading" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
            </svg>
            {{ loading ? 'Memproses...' : 'Masuk ke Dashboard' }}
          </button>
        </form>

        <div class="mt-6 text-center">
          <router-link to="/" class="inline-flex items-center gap-2 text-sm text-[#A8BC9A] hover:text-white transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Kembali ke Beranda
          </router-link>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted, onUnmounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../stores/auth'

const router  = useRouter()
const auth    = useAuthStore()
const loading = ref(false)
const error   = ref('')
const showPwd = ref(false)
const bgCanvas = ref(null)
let animationFrameId = null
let resizeHandler = null

const form = reactive({ email: '', password: '' })

onMounted(() => {
  initAnimation()
})

onUnmounted(() => {
  if (animationFrameId) cancelAnimationFrame(animationFrameId)
  if (resizeHandler) window.removeEventListener('resize', resizeHandler)
})

function initAnimation() {
  const canvas = bgCanvas.value
  if (!canvas) return
  const ctx = canvas.getContext('2d')

  let width = canvas.width = window.innerWidth
  let height = canvas.height = window.innerHeight

  resizeHandler = () => {
    width = canvas.width = window.innerWidth
    height = canvas.height = window.innerHeight
  }
  window.addEventListener('resize', resizeHandler)

  const particles = []

  const particleCount = Math.min(Math.floor(window.innerWidth / 15), 100)

  for(let i = 0; i < particleCount; i++) {
    particles.push({
      x: Math.random() * width,
      y: Math.random() * height,
      vx: (Math.random() - 0.5) * 0.6,
      vy: (Math.random() - 0.5) * 0.6,
      radius: Math.random() * 2 + 1
    })
  }

  function draw() {
    ctx.clearRect(0, 0, width, height)
    ctx.fillStyle = '#E8E2D9' // parchment color for dots

    for(let i = 0; i < particles.length; i++) {
      let p = particles[i]
      p.x += p.vx
      p.y += p.vy

      if(p.x < 0 || p.x > width) p.vx *= -1
      if(p.y < 0 || p.y > height) p.vy *= -1

      ctx.beginPath()
      ctx.arc(p.x, p.y, p.radius, 0, Math.PI * 2)
      ctx.fill()

      for(let j = i + 1; j < particles.length; j++) {
        let p2 = particles[j]
        let dx = p.x - p2.x
        let dy = p.y - p2.y
        let dist = Math.sqrt(dx*dx + dy*dy)

        if(dist < 150) {
          ctx.beginPath()

          ctx.strokeStyle = `rgba(168, 188, 154, ${1 - dist/150})`
          ctx.lineWidth = 0.5
          ctx.moveTo(p.x, p.y)
          ctx.lineTo(p2.x, p2.y)
          ctx.stroke()
        }
      }
    }
    animationFrameId = requestAnimationFrame(draw)
  }
  draw()
}

async function handleLogin() {
  loading.value = true
  error.value   = ''
  try {
    const res = await auth.login(form.email, form.password)
    if (res.status) {
      router.push('/dashboard')
    } else {
      error.value = res.message || 'Login gagal'
    }
  } catch (e) {
    error.value = e.response?.data?.message || 'Terjadi kesalahan. Coba lagi.'
  } finally {
    loading.value = false
  }
}
</script>

<style scoped>
.fade-enter-active, .fade-leave-active { transition: opacity 0.3s; }
.fade-enter-from, .fade-leave-to { opacity: 0; }
</style>
