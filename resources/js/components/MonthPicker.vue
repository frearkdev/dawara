<template>
  <div class="w-full">
    <div class="flex items-center justify-between mb-3">
      <button type="button" @click="prevMonth" class="p-2 rounded-md text-white hover:bg-white/5"><-</button>
      <div class="text-sm font-medium text-stone-100 flex-1 text-center">{{ monthLabel }}</div>
      <button type="button" @click="nextMonth" class="p-2 rounded-md text-white hover:bg-white/5">-></button>
    </div>

    <div class="grid grid-cols-7 gap-2 text-center text-xs mb-2 text-stone-400">
      <div v-for="d in weekDays" :key="d">{{ d }}</div>
    </div>

    <div class="grid grid-cols-7 gap-2 text-center">
      <template v-for="(cell, idx) in calendarCells" :key="cell ? cell.iso : 'empty-'+idx">
        <button v-if="cell"
          type="button"
          :disabled="cell.disabled"
          @click="select(cell)"
          :aria-pressed="cell.selected"
          class="mx-auto h-10 w-10 rounded-full flex items-center justify-center transition-transform transition-colors focus:outline-none"
          :class="cell.selected ? 'bg-white text-stone-900 scale-[1.03] shadow-md' : cell.disabled ? 'text-stone-500 opacity-40' : 'text-stone-200 hover:bg-white/5'">
          {{ cell.day }}
        </button>
        <div v-else class="h-10 w-10" />
      </template>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, watch } from 'vue'

const props = defineProps<{
  modelValue?: string | null
  minDate?: string
}>()
const emit = defineEmits(['update:modelValue'])

const pad = (n: number) => n.toString().padStart(2, '0')
const toISO = (d: Date) => `${d.getFullYear()}-${pad(d.getMonth()+1)}-${pad(d.getDate())}`

const selected = ref<string | null>(props.modelValue ?? null)
const init = props.modelValue ? new Date(props.modelValue + 'T00:00:00') : new Date()
const currentMonth = ref(new Date(init.getFullYear(), init.getMonth(), 1))

watch(() => props.modelValue, v => {
 selected.value = v ?? null 
})
watch(selected, v => {
 emit('update:modelValue', v) 
})

const monthLabel = computed(() => currentMonth.value.toLocaleString('nl-NL', { month: 'long', year: 'numeric' }))
const weekDays = ['Ma','Di','Wo','Do','Vr','Za','Zo']

const calendarCells = computed(() => {
  const year = currentMonth.value.getFullYear()
  const month = currentMonth.value.getMonth()
  const firstDay = new Date(year, month, 1)
  const startIndex = (firstDay.getDay() + 6) % 7 // Monday-first
  const days = new Date(year, month + 1, 0).getDate()
  const cells: Array<null | { day: number; iso: string; disabled: boolean; selected: boolean }> = []

  for (let i = 0; i < startIndex; i++) {
cells.push(null)
}

  for (let d = 1; d <= days; d++) {
    const dt = new Date(year, month, d)
    const iso = toISO(dt)
    const disabled = props.minDate ? iso < props.minDate : false
    const sel = selected.value === iso
    cells.push({ day: d, iso, disabled, selected: sel })
  }

  return cells
})

function prevMonth() {
  currentMonth.value = new Date(currentMonth.value.getFullYear(), currentMonth.value.getMonth() - 1, 1)
}
function nextMonth() {
  currentMonth.value = new Date(currentMonth.value.getFullYear(), currentMonth.value.getMonth() + 1, 1)
}

function select(cell: { iso: string; disabled: boolean }) {
  if (cell.disabled) {
return
}

  selected.value = cell.iso
}
</script>

<style scoped>
.h-9 { height: 2.25rem; }
.w-9 { width: 2.25rem; }
.h-10 { height: 2.5rem; }
.w-10 { width: 2.5rem; }
button[disabled] { cursor: default; }
</style>
