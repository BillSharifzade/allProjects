<template>
  <div class="p-6 space-y-6">
    <!-- Portfolio Summary -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
      <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-200">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-sm text-gray-600">Total Value</p>
            <p class="text-2xl font-bold text-gray-900">{{ formatPrice(totalValue) }}</p>
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
            <p class="text-sm text-gray-600">Total Profit/Loss</p>
            <p class="text-2xl font-bold" :class="totalPL >= 0 ? 'text-green-600' : 'text-red-600'">
              {{ formatPrice(totalPL) }}
            </p>
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
            <p class="text-sm text-gray-600">24h Change</p>
            <p class="text-2xl font-bold" :class="dailyChange >= 0 ? 'text-green-600' : 'text-red-600'">
              {{ dailyChange >= 0 ? '+' : '' }}{{ dailyChange.toFixed(2) }}%
            </p>
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
            <p class="text-sm text-gray-600">Assets</p>
            <p class="text-2xl font-bold text-gray-900">{{ portfolio.length }}</p>
          </div>
          <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
            <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
            </svg>
          </div>
        </div>
      </div>
    </div>

    <!-- Add Asset Button -->
    <div class="flex justify-between items-center">
      <h2 class="text-xl font-semibold text-gray-900">Your Portfolio</h2>
      <button
        @click="showAddAssetModal = true"
        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors flex items-center space-x-2"
      >
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
        </svg>
        <span>Add Asset</span>
      </button>
    </div>

    <!-- Portfolio Assets -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
      <div v-if="portfolio.length === 0" class="p-12 text-center">
        <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
        </svg>
        <h3 class="text-lg font-medium text-gray-900 mb-2">No assets in your portfolio</h3>
        <p class="text-gray-500 mb-4">Start building your portfolio by adding your first cryptocurrency asset.</p>
        <button
          @click="showAddAssetModal = true"
          class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors"
        >
          Add Your First Asset
        </button>
      </div>

      <div v-else class="overflow-x-auto">
        <table class="w-full">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Asset</th>
              <th class="px-6 py-4 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Holdings</th>
              <th class="px-6 py-4 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Avg. Price</th>
              <th class="px-6 py-4 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Current Price</th>
              <th class="px-6 py-4 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Value</th>
              <th class="px-6 py-4 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">P/L</th>
              <th class="px-6 py-4 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <tr v-for="asset in portfolio" :key="asset.id" class="hover:bg-gray-50">
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="flex items-center">
                  <img :src="asset.image" :alt="asset.name" class="w-8 h-8 rounded-full mr-3">
                  <div>
                    <div class="text-sm font-medium text-gray-900">{{ asset.name }}</div>
                    <div class="text-sm text-gray-500">{{ asset.symbol.toUpperCase() }}</div>
                  </div>
                </div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-right text-sm text-gray-900">
                {{ asset.quantity.toFixed(6) }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-right text-sm text-gray-900">
                {{ formatPrice(asset.averagePrice) }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-right text-sm text-gray-900">
                {{ formatPrice(asset.currentPrice) }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium text-gray-900">
                {{ formatPrice(asset.value) }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-right text-sm" :class="asset.profitLoss >= 0 ? 'text-green-600' : 'text-red-600'">
                {{ asset.profitLoss >= 0 ? '+' : '' }}{{ formatPrice(asset.profitLoss) }}
                <br>
                <span class="text-xs">{{ asset.profitLossPercentage >= 0 ? '+' : '' }}{{ asset.profitLossPercentage.toFixed(2) }}%</span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                <button
                  @click="editAsset(asset)"
                  class="text-blue-600 hover:text-blue-800 mr-3"
                >
                  Edit
                </button>
                <button
                  @click="removeAsset(asset.id)"
                  class="text-red-600 hover:text-red-800"
                >
                  Remove
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Add Asset Modal -->
    <div v-if="showAddAssetModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white rounded-xl p-6 w-full max-w-md mx-4">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Add Asset</h3>
        
        <div class="space-y-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Cryptocurrency</label>
            <select
              v-model="newAsset.cryptoId"
              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            >
              <option value="">Select a cryptocurrency</option>
              <option v-for="crypto in cryptocurrencies" :key="crypto.id" :value="crypto.id">
                {{ crypto.name }} ({{ crypto.symbol.toUpperCase() }})
              </option>
            </select>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Quantity</label>
            <input
              v-model.number="newAsset.quantity"
              type="number"
              step="0.000001"
              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              placeholder="0.000000"
            >
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Average Price</label>
            <input
              v-model.number="newAsset.averagePrice"
              type="number"
              step="0.01"
              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              placeholder="0.00"
            >
          </div>
        </div>

        <div class="flex justify-end space-x-3 mt-6">
          <button
            @click="showAddAssetModal = false"
            class="px-4 py-2 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors"
          >
            Cancel
          </button>
          <button
            @click="addAsset"
            :disabled="!newAsset.cryptoId || !newAsset.quantity || !newAsset.averagePrice"
            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
          >
            Add Asset
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue'
import { useCryptoStore } from '@/stores/crypto'

const cryptoStore = useCryptoStore()
const { cryptocurrencies, formatPrice } = cryptoStore

// Local state
const showAddAssetModal = ref(false)
const portfolio = ref<any[]>([])
const newAsset = ref({
  cryptoId: '',
  quantity: 0,
  averagePrice: 0
})

// Computed properties
const totalValue = computed(() => 
  portfolio.value.reduce((sum, asset) => sum + asset.value, 0)
)

const totalPL = computed(() => 
  portfolio.value.reduce((sum, asset) => sum + asset.profitLoss, 0)
)

const dailyChange = computed(() => {
  if (portfolio.value.length === 0) return 0
  
  const totalYesterday = portfolio.value.reduce((sum, asset) => {
    const yesterdayPrice = asset.currentPrice / (1 + asset.priceChange24h / 100)
    return sum + (asset.quantity * yesterdayPrice)
  }, 0)
  
  return ((totalValue.value - totalYesterday) / totalYesterday) * 100
})

// Methods
const addAsset = () => {
  const crypto = cryptocurrencies.value.find(c => c.id === newAsset.value.cryptoId)
  if (!crypto) return

  const asset = {
    id: Date.now().toString(),
    cryptoId: newAsset.value.cryptoId,
    name: crypto.name,
    symbol: crypto.symbol,
    image: crypto.image,
    quantity: newAsset.value.quantity,
    averagePrice: newAsset.value.averagePrice,
    currentPrice: crypto.current_price,
    priceChange24h: crypto.price_change_percentage_24h,
    value: newAsset.value.quantity * crypto.current_price,
    profitLoss: (newAsset.value.quantity * crypto.current_price) - (newAsset.value.quantity * newAsset.value.averagePrice),
    profitLossPercentage: ((crypto.current_price - newAsset.value.averagePrice) / newAsset.value.averagePrice) * 100
  }

  portfolio.value.push(asset)
  showAddAssetModal.value = false
  
  // Reset form
  newAsset.value = {
    cryptoId: '',
    quantity: 0,
    averagePrice: 0
  }
}

const editAsset = (asset: any) => {
  // Implementation for editing assets
  console.log('Edit asset:', asset)
}

const removeAsset = (assetId: string) => {
  portfolio.value = portfolio.value.filter(asset => asset.id !== assetId)
}
</script>