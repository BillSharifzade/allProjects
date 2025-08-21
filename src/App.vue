<template>
  <div id="app" class="min-h-screen bg-gray-100">
    <div class="flex h-screen">
      <!-- Sidebar -->
      <div class="w-80 bg-white shadow-lg border-r border-gray-200">
        <div class="telegram-gradient p-4">
          <div class="flex items-center space-x-3">
            <div class="w-10 h-10 bg-white rounded-full flex items-center justify-center">
              <svg class="w-6 h-6 text-telegram-blue" fill="currentColor" viewBox="0 0 24 24">
                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
              </svg>
            </div>
            <div>
              <h1 class="text-white font-semibold text-lg">CryptoBot</h1>
              <p class="text-white text-sm opacity-90">Live cryptocurrency data</p>
            </div>
          </div>
        </div>
        
        <!-- Navigation -->
        <nav class="p-4">
          <router-link 
            v-for="item in navigationItems" 
            :key="item.path"
            :to="item.path"
            class="flex items-center space-x-3 p-3 rounded-lg mb-2 transition-colors hover:bg-gray-100"
            :class="{ 'bg-blue-50 text-blue-600': $route.path === item.path }"
          >
            <component :is="item.icon" class="w-5 h-5" />
            <span>{{ item.name }}</span>
          </router-link>
        </nav>

        <!-- Market Summary -->
        <div class="p-4 border-t border-gray-200">
          <h3 class="font-semibold text-gray-700 mb-3">Market Summary</h3>
          <div class="space-y-2">
            <div class="flex justify-between text-sm">
              <span class="text-gray-600">Total Market Cap:</span>
              <span class="font-medium">{{ formatMarketCap(totalMarketCap) }}</span>
            </div>
            <div class="flex justify-between text-sm">
              <span class="text-gray-600">Active Coins:</span>
              <span class="font-medium">{{ cryptocurrencies.length }}</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Main Content -->
      <div class="flex-1 flex flex-col">
        <!-- Header -->
        <header class="bg-white shadow-sm border-b border-gray-200 p-4">
          <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold text-gray-800">
              {{ currentPageTitle }}
            </h2>
            <div class="flex items-center space-x-4">
              <button 
                @click="refreshData"
                :disabled="loading"
                class="flex items-center space-x-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50"
              >
                <svg v-if="loading" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                </svg>
                <span>Refresh</span>
              </button>
            </div>
          </div>
        </header>

        <!-- Router View -->
        <main class="flex-1 overflow-auto">
          <router-view />
        </main>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import { useCryptoStore } from '@/stores/crypto'
import {
  HomeIcon,
  ChartBarIcon,
  CurrencyDollarIcon,
  BellIcon,
  CogIcon
} from '@heroicons/vue/24/outline'

const route = useRoute()
const cryptoStore = useCryptoStore()

const navigationItems = [
  { name: 'Dashboard', path: '/', icon: HomeIcon },
  { name: 'Market', path: '/market', icon: ChartBarIcon },
  { name: 'Portfolio', path: '/portfolio', icon: CurrencyDollarIcon },
  { name: 'Alerts', path: '/alerts', icon: BellIcon },
  { name: 'Settings', path: '/settings', icon: CogIcon }
]

const currentPageTitle = computed(() => {
  const currentItem = navigationItems.find(item => item.path === route.path)
  return currentItem?.name || 'CryptoBot'
})

const { loading, totalMarketCap, cryptocurrencies, fetchCryptocurrencies, formatMarketCap } = cryptoStore

const refreshData = () => {
  fetchCryptocurrencies()
}

onMounted(() => {
  fetchCryptocurrencies()
})
</script>

<style scoped>
.router-link-active {
  @apply bg-blue-50 text-blue-600;
}
</style>