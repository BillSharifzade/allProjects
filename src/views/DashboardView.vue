<template>
  <div class="p-6 space-y-6">
    <!-- Welcome Message -->
    <div class="crypto-gradient rounded-xl p-6 text-white">
      <h1 class="text-2xl font-bold mb-2">Welcome to CryptoBot! 🤖</h1>
      <p class="opacity-90">Get real-time cryptocurrency data and market insights</p>
    </div>

    <!-- Market Overview Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
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
            <p class="text-2xl font-bold text-gray-900">{{ cryptocurrencies.length }}</p>
          </div>
          <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
            <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"></path>
            </svg>
          </div>
        </div>
      </div>

      <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-200">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-sm text-gray-600">Market Sentiment</p>
            <p class="text-2xl font-bold" :class="marketSentimentColor">{{ marketSentiment }}</p>
          </div>
          <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
            <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
            </svg>
          </div>
        </div>
      </div>
    </div>

    <!-- Top Gainers and Losers -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
      <!-- Top Gainers -->
      <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <div class="p-6 border-b border-gray-200">
          <h3 class="text-lg font-semibold text-gray-900 flex items-center">
            <svg class="w-5 h-5 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
            </svg>
            Top Gainers (24h)
          </h3>
        </div>
        <div class="p-6">
          <div v-if="loading" class="flex justify-center py-8">
            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-green-500"></div>
          </div>
          <div v-else class="space-y-4">
            <div 
              v-for="crypto in topGainers" 
              :key="crypto.id"
              class="flex items-center justify-between p-3 rounded-lg hover:bg-gray-50 cursor-pointer transition-colors"
              @click="selectCrypto(crypto)"
            >
              <div class="flex items-center space-x-3">
                <img :src="crypto.image" :alt="crypto.name" class="w-8 h-8 rounded-full">
                <div>
                  <p class="font-medium text-gray-900">{{ crypto.name }}</p>
                  <p class="text-sm text-gray-500">{{ crypto.symbol.toUpperCase() }}</p>
                </div>
              </div>
              <div class="text-right">
                <p class="font-medium text-gray-900">{{ formatPrice(crypto.current_price) }}</p>
                <p class="text-sm text-green-500">+{{ crypto.price_change_percentage_24h.toFixed(2) }}%</p>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Top Losers -->
      <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <div class="p-6 border-b border-gray-200">
          <h3 class="text-lg font-semibold text-gray-900 flex items-center">
            <svg class="w-5 h-5 text-red-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"></path>
            </svg>
            Top Losers (24h)
          </h3>
        </div>
        <div class="p-6">
          <div v-if="loading" class="flex justify-center py-8">
            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-red-500"></div>
          </div>
          <div v-else class="space-y-4">
            <div 
              v-for="crypto in topLosers" 
              :key="crypto.id"
              class="flex items-center justify-between p-3 rounded-lg hover:bg-gray-50 cursor-pointer transition-colors"
              @click="selectCrypto(crypto)"
            >
              <div class="flex items-center space-x-3">
                <img :src="crypto.image" :alt="crypto.name" class="w-8 h-8 rounded-full">
                <div>
                  <p class="font-medium text-gray-900">{{ crypto.name }}</p>
                  <p class="text-sm text-gray-500">{{ crypto.symbol.toUpperCase() }}</p>
                </div>
              </div>
              <div class="text-right">
                <p class="font-medium text-gray-900">{{ formatPrice(crypto.current_price) }}</p>
                <p class="text-sm text-red-500">{{ crypto.price_change_percentage_24h.toFixed(2) }}%</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Recent Market Activity -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
      <div class="p-6 border-b border-gray-200">
        <h3 class="text-lg font-semibold text-gray-900">Recent Market Activity</h3>
      </div>
      <div class="p-6">
        <div v-if="loading" class="flex justify-center py-8">
          <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-500"></div>
        </div>
        <div v-else class="space-y-4">
          <div 
            v-for="crypto in recentActivity" 
            :key="crypto.id"
            class="flex items-center justify-between p-4 rounded-lg border border-gray-100 hover:border-gray-200 transition-colors"
          >
            <div class="flex items-center space-x-4">
              <img :src="crypto.image" :alt="crypto.name" class="w-10 h-10 rounded-full">
              <div>
                <p class="font-medium text-gray-900">{{ crypto.name }}</p>
                <p class="text-sm text-gray-500">Rank #{{ crypto.market_cap_rank }}</p>
              </div>
            </div>
            <div class="text-right">
              <p class="font-medium text-gray-900">{{ formatPrice(crypto.current_price) }}</p>
              <p class="text-sm" :class="getPriceChangeColor(crypto.price_change_percentage_24h)">
                {{ crypto.price_change_percentage_24h >= 0 ? '+' : '' }}{{ crypto.price_change_percentage_24h.toFixed(2) }}%
              </p>
            </div>
            <div class="text-right">
              <p class="text-sm text-gray-500">Market Cap</p>
              <p class="font-medium text-gray-900">{{ formatMarketCap(crypto.market_cap) }}</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import { useRouter } from 'vue-router'
import { useCryptoStore } from '@/stores/crypto'

const router = useRouter()
const cryptoStore = useCryptoStore()

const {
  cryptocurrencies,
  loading,
  topGainers,
  topLosers,
  totalMarketCap,
  formatPrice,
  formatMarketCap,
  getPriceChangeColor,
  setSelectedCurrency
} = cryptoStore

const totalVolume = computed(() => 
  cryptocurrencies.value.reduce((sum, crypto) => sum + crypto.total_volume, 0)
)

const marketSentiment = computed(() => {
  const gainers = topGainers.value.length
  const losers = topLosers.value.length
  
  if (gainers > losers * 2) return 'Bullish'
  if (losers > gainers * 2) return 'Bearish'
  return 'Neutral'
})

const marketSentimentColor = computed(() => {
  switch (marketSentiment.value) {
    case 'Bullish': return 'text-green-600'
    case 'Bearish': return 'text-red-600'
    default: return 'text-yellow-600'
  }
})

const recentActivity = computed(() => 
  cryptocurrencies.value.slice(0, 10)
)

const selectCrypto = (crypto: any) => {
  setSelectedCurrency(crypto)
  router.push(`/market/${crypto.id}`)
}
</script>