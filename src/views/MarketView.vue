<template>
  <div class="p-6 space-y-6">
    <!-- Search and Filters -->
    <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-200">
      <div class="flex flex-col md:flex-row gap-4">
        <div class="flex-1">
          <div class="relative">
            <input
              v-model="searchQuery"
              type="text"
              placeholder="Search cryptocurrencies..."
              class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            >
            <svg class="absolute left-3 top-3.5 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
          </div>
        </div>
        <div class="flex gap-2">
          <select
            v-model="sortBy"
            class="px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
          >
            <option value="market_cap">Market Cap</option>
            <option value="price">Price</option>
            <option value="volume">Volume</option>
            <option value="change">24h Change</option>
          </select>
          <button
            @click="toggleSortOrder"
            class="px-4 py-3 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors"
          >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4h13M3 8h9m-9 4h6m4 0l4-4m0 0l4 4m-4-4v12"></path>
            </svg>
          </button>
        </div>
      </div>
    </div>

    <!-- Market Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
      <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-200">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-sm text-gray-600">Total Market Cap</p>
            <p class="text-2xl font-bold text-gray-900">{{ formatMarketCap(totalMarketCap) }}</p>
          </div>
          <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
            </svg>
          </div>
        </div>
      </div>

      <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-200">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-sm text-gray-600">24h Volume</p>
            <p class="text-2xl font-bold text-gray-900">{{ formatMarketCap(totalVolume) }}</p>
          </div>
          <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
            </svg>
          </div>
        </div>
      </div>

      <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-200">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-sm text-gray-600">Active Coins</p>
            <p class="text-2xl font-bold text-gray-900">{{ filteredCryptos.length }}</p>
          </div>
          <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
            <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"></path>
            </svg>
          </div>
        </div>
      </div>
    </div>

    <!-- Cryptocurrency List -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
      <div class="p-6 border-b border-gray-200">
        <h3 class="text-lg font-semibold text-gray-900">All Cryptocurrencies</h3>
      </div>
      
      <div v-if="loading" class="flex justify-center py-12">
        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-500"></div>
      </div>
      
      <div v-else-if="error" class="p-6 text-center">
        <p class="text-red-600">{{ error }}</p>
        <button 
          @click="fetchCryptocurrencies"
          class="mt-4 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700"
        >
          Retry
        </button>
      </div>
      
      <div v-else class="overflow-x-auto">
        <table class="w-full">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">#</th>
              <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
              <th class="px-6 py-4 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
              <th class="px-6 py-4 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">24h %</th>
              <th class="px-6 py-4 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">7d %</th>
              <th class="px-6 py-4 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Market Cap</th>
              <th class="px-6 py-4 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Volume(24h)</th>
              <th class="px-6 py-4 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Circulating Supply</th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <tr 
              v-for="crypto in paginatedCryptos" 
              :key="crypto.id"
              class="hover:bg-gray-50 cursor-pointer transition-colors"
              @click="selectCrypto(crypto)"
            >
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                {{ crypto.market_cap_rank }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="flex items-center">
                  <img :src="crypto.image" :alt="crypto.name" class="w-8 h-8 rounded-full mr-3">
                  <div>
                    <div class="text-sm font-medium text-gray-900">{{ crypto.name }}</div>
                    <div class="text-sm text-gray-500">{{ crypto.symbol.toUpperCase() }}</div>
                  </div>
                </div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium text-gray-900">
                {{ formatPrice(crypto.current_price) }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-right text-sm" :class="getPriceChangeColor(crypto.price_change_percentage_24h)">
                {{ crypto.price_change_percentage_24h >= 0 ? '+' : '' }}{{ crypto.price_change_percentage_24h.toFixed(2) }}%
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-right text-sm text-gray-500">
                <!-- Placeholder for 7d change -->
                --
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-right text-sm text-gray-900">
                {{ formatMarketCap(crypto.market_cap) }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-right text-sm text-gray-900">
                {{ formatMarketCap(crypto.total_volume) }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-right text-sm text-gray-900">
                {{ formatSupply(crypto.circulating_supply) }} {{ crypto.symbol.toUpperCase() }}
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <div v-if="!loading && !error" class="px-6 py-4 border-t border-gray-200">
        <div class="flex items-center justify-between">
          <div class="text-sm text-gray-700">
            Showing {{ startIndex + 1 }} to {{ endIndex }} of {{ filteredCryptos.length }} results
          </div>
          <div class="flex space-x-2">
            <button
              @click="previousPage"
              :disabled="currentPage === 1"
              class="px-3 py-2 text-sm border border-gray-300 rounded-lg hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
            >
              Previous
            </button>
            <span class="px-3 py-2 text-sm text-gray-700">
              Page {{ currentPage }} of {{ totalPages }}
            </span>
            <button
              @click="nextPage"
              :disabled="currentPage === totalPages"
              class="px-3 py-2 text-sm border border-gray-300 rounded-lg hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
            >
              Next
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, watch } from 'vue'
import { useRouter } from 'vue-router'
import { useCryptoStore } from '@/stores/crypto'

const router = useRouter()
const cryptoStore = useCryptoStore()

const {
  cryptocurrencies,
  loading,
  error,
  totalMarketCap,
  formatPrice,
  formatMarketCap,
  getPriceChangeColor,
  searchCryptocurrencies,
  setSelectedCurrency,
  fetchCryptocurrencies
} = cryptoStore

// Local state
const searchQuery = ref('')
const sortBy = ref('market_cap')
const sortOrder = ref<'asc' | 'desc'>('desc')
const currentPage = ref(1)
const itemsPerPage = 20

// Computed properties
const filteredCryptos = computed(() => {
  let filtered = searchQuery.value 
    ? searchCryptocurrencies(searchQuery.value)
    : cryptocurrencies.value

  // Sort the filtered results
  filtered = [...filtered].sort((a, b) => {
    let aValue: number
    let bValue: number

    switch (sortBy.value) {
      case 'price':
        aValue = a.current_price
        bValue = b.current_price
        break
      case 'volume':
        aValue = a.total_volume
        bValue = b.total_volume
        break
      case 'change':
        aValue = a.price_change_percentage_24h
        bValue = b.price_change_percentage_24h
        break
      default:
        aValue = a.market_cap
        bValue = b.market_cap
    }

    return sortOrder.value === 'asc' ? aValue - bValue : bValue - aValue
  })

  return filtered
})

const totalPages = computed(() => Math.ceil(filteredCryptos.value.length / itemsPerPage))

const startIndex = computed(() => (currentPage.value - 1) * itemsPerPage)
const endIndex = computed(() => Math.min(startIndex.value + itemsPerPage, filteredCryptos.value.length))

const paginatedCryptos = computed(() => 
  filteredCryptos.value.slice(startIndex.value, endIndex.value)
)

const totalVolume = computed(() => 
  cryptocurrencies.value.reduce((sum, crypto) => sum + crypto.total_volume, 0)
)

// Methods
const toggleSortOrder = () => {
  sortOrder.value = sortOrder.value === 'asc' ? 'desc' : 'asc'
}

const previousPage = () => {
  if (currentPage.value > 1) {
    currentPage.value--
  }
}

const nextPage = () => {
  if (currentPage.value < totalPages.value) {
    currentPage.value++
  }
}

const selectCrypto = (crypto: any) => {
  setSelectedCurrency(crypto)
  router.push(`/market/${crypto.id}`)
}

const formatSupply = (supply: number) => {
  if (supply >= 1e9) {
    return `${(supply / 1e9).toFixed(2)}B`
  } else if (supply >= 1e6) {
    return `${(supply / 1e6).toFixed(2)}M`
  } else if (supply >= 1e3) {
    return `${(supply / 1e3).toFixed(2)}K`
  }
  return supply.toLocaleString()
}

// Watchers
watch(searchQuery, () => {
  currentPage.value = 1
})

watch(sortBy, () => {
  currentPage.value = 1
})
</script>