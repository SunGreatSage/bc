<template>
  <div class="flex flex-wrap items-end justify-center gap-x-2 gap-y-1">
    <template v-for="(item, idx) in displayItems" :key="idx">
      <div
        v-if="item.type === 'plus'"
        class="pb-4 text-lg font-bold text-gray-400 select-none"
        aria-hidden="true"
      >
        +
      </div>

      <div v-else class="flex w-10 flex-col items-center">
        <div
          class="flex h-9 w-9 items-center justify-center rounded-full bg-white text-sm font-bold text-gray-900 shadow"
          :class="[
            'border-[3px]',
            getWaveBorderClass(item.num),
            item.isSpecial ? 'ring-1 ring-black/10' : '',
            item.ballIndex === activeIndex ? 'scale-110 animate-pulse ring-2 ring-black/20' : 'scale-100',
            'transition-transform duration-150'
          ]"
          style="box-shadow: inset -2px -2px 4px rgba(0,0,0,0.08), inset 2px 2px 4px rgba(255,255,255,0.9), 0 3px 8px rgba(0,0,0,0.18);"
        >
          {{ formatNum(item.num) }}
        </div>
        <div class="mt-1 text-[10px] leading-none text-gray-600">
          {{ getZodiac(item.num) }}/{{ getWuXing(item.num) }}
        </div>
      </div>
    </template>
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  numbers: {
    type: Array,
    default: () => []
  },
  specialIndex: {
    type: Number,
    default: 6
  },
  activeIndex: {
    type: Number,
    default: -1
  },
  year: {
    type: Number,
    default: new Date().getFullYear()
  }
})

const displayItems = computed(() => {
  const raw = Array.isArray(props.numbers) ? props.numbers : []
  const items = raw.map((num, index) => ({
    type: 'ball',
    num,
    isSpecial: index === props.specialIndex,
    ballIndex: index
  }))

  if (items.length > props.specialIndex) {
    items.splice(props.specialIndex, 0, { type: 'plus' })
  }

  return items
})

const formatNum = (num) => {
  if (num === '?' || num === '？') return '?'

  const n = parseInt(String(num), 10)
  if (!Number.isFinite(n)) return '?'
  return String(n).padStart(2, '0')
}

const WAVE = {
  red: new Set([1, 2, 7, 8, 12, 13, 18, 19, 23, 24, 29, 30, 34, 35, 40, 45, 46]),
  blue: new Set([3, 4, 9, 10, 14, 15, 20, 25, 26, 31, 36, 37, 41, 42, 47, 48]),
  green: new Set([5, 6, 11, 16, 17, 21, 22, 27, 28, 32, 33, 38, 39, 43, 44, 49])
}

const getWaveBorderClass = (num) => {
  const n = parseInt(String(num), 10)
  if (!Number.isFinite(n)) return 'border-gray-300'
  if (WAVE.red.has(n)) return 'border-red-500'
  if (WAVE.blue.has(n)) return 'border-blue-500'
  if (WAVE.green.has(n)) return 'border-green-500'
  return 'border-gray-300'
}

const wuxingByNumber = (() => {
  const map = new Map()

  ;[
    ['金', [5, 6, 19, 20, 27, 28, 35, 36, 49]],
    ['木', [1, 2, 9, 10, 17, 18, 31, 32, 39, 40, 47, 48]],
    ['水', [7, 8, 15, 16, 23, 24, 37, 38, 45, 46]],
    ['火', [3, 4, 11, 12, 25, 26, 33, 34, 41, 42]],
    ['土', [13, 14, 21, 22, 29, 30, 43, 44]]
  ].forEach(([name, nums]) => {
    nums.forEach((n) => map.set(n, name))
  })

  return map
})()

const getWuXing = (num) => {
  const n = parseInt(String(num), 10)
  if (!Number.isFinite(n)) return '--'
  return wuxingByNumber.get(n) || '--'
}

const zodiacOrder = ['鼠', '牛', '虎', '兔', '龙', '蛇', '马', '羊', '猴', '鸡', '狗', '猪']

const yearZodiacIndex = computed(() => {
  const y = parseInt(String(props.year), 10)
  if (!Number.isFinite(y)) return 0
  return ((y - 4) % 12 + 12) % 12
})

const getZodiac = (num) => {
  const n = parseInt(String(num), 10)
  if (!Number.isFinite(n)) return '--'

  const baseIndex = ((n - 1) % 12 + 12) % 12
  const zodiacIndex = (yearZodiacIndex.value - baseIndex + 12) % 12
  return zodiacOrder[zodiacIndex] || '--'
}
</script>
