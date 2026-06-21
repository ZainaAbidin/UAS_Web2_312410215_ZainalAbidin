<template>
  <teleport to="body">
    <div class="modal-overlay" @click.self="$emit('close')">
      <div class="modal-box" :style="{ maxWidth: width }">
                <div class="modal-header">
          <h3 class="text-lg font-semibold text-gray-900">{{ title }}</h3>
          <button @click="$emit('close')" class="p-1.5 rounded-lg text-gray-400 hover:bg-gray-100 hover:text-gray-600 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
          </button>
        </div>

                <div class="modal-body">
          <slot />
        </div>

                <div class="modal-footer">
          <button @click="$emit('close')" class="btn-secondary">Batal</button>
          <button @click="$emit('confirm')" :disabled="loading" class="btn-primary">
            <svg v-if="loading" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
            </svg>
            {{ confirmText }}
          </button>
        </div>
      </div>
    </div>
  </teleport>
</template>

<script setup>
defineProps({
  title:       { type: String, default: 'Form' },
  confirmText: { type: String, default: 'Simpan' },
  loading:     { type: Boolean, default: false },
  width:       { type: String, default: '520px' },
})
defineEmits(['close', 'confirm'])
</script>
