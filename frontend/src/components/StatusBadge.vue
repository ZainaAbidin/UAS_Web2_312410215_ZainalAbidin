<template>
  <span class="badge" :class="badgeClass">{{ label }}</span>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({ status: String })

const config = {
  available:   { class: 'badge-success', label: 'Tersedia' },
  unavailable: { class: 'badge-danger',  label: 'Tidak Tersedia' },
  active:      { class: 'badge-success', label: 'Aktif' },
  inactive:    { class: 'badge-gray',    label: 'Nonaktif' },
  pending:     { class: 'badge-warning', label: 'Menunggu' },
  returned:    { class: 'badge-info',    label: 'Dikembalikan' },
  overdue:     { class: 'badge-ochre',   label: 'Terlambat' },
  author:      { class: 'badge-purple',  label: 'Penulis' },
  publisher:   { class: 'badge-info',    label: 'Penerbit' },
  book:        { class: 'badge-info',    label: 'Buku' },
  comic:       { class: 'badge-purple',  label: 'Komik' },
}

const badgeClass = computed(() => config[props.status]?.class || 'badge-gray')
const label      = computed(() => config[props.status]?.label || props.status)
</script>
