<template>
  <div class="p-6 space-y-6">
    <!-- Alerts Summary -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
      <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-200">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-sm text-gray-600">Active Alerts</p>
            <p class="text-2xl font-bold text-gray-900">{{ activeAlerts.length }}</p>
          </div>
          <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5zM4.19 4.19A4 4 0 004 6v6a4 4 0 004 4h6a4 4 0 004-4V6a4 4 0 00-4-4H8a4 4 0 00-2.81 1.19z"></path>
            </svg>
          </div>
        </div>
      </div>

      <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-200">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-sm text-gray-600">Triggered Today</p>
            <p class="text-2xl font-bold text-gray-900">{{ triggeredToday }}</p>
          </div>
          <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
            <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
            </svg>
          </div>
        </div>
      </div>

      <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-200">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-sm text-gray-600">Notifications</p>
            <p class="text-2xl font-bold text-gray-900">{{ notificationsEnabled ? 'On' : 'Off' }}</p>
          </div>
          <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5zM4.19 4.19A4 4 0 004 6v6a4 4 0 004 4h6a4 4 0 004-4V6a4 4 0 00-4-4H8a4 4 0 00-2.81 1.19z"></path>
            </svg>
          </div>
        </div>
      </div>
    </div>

    <!-- Add Alert Button -->
    <div class="flex justify-between items-center">
      <h2 class="text-xl font-semibold text-gray-900">Price Alerts</h2>
      <button
        @click="showAddAlertModal = true"
        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors flex items-center space-x-2"
      >
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
        </svg>
        <span>Add Alert</span>
      </button>
    </div>

    <!-- Alerts List -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
      <div v-if="alerts.length === 0" class="p-12 text-center">
        <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5zM4.19 4.19A4 4 0 004 6v6a4 4 0 004 4h6a4 4 0 004-4V6a4 4 0 00-4-4H8a4 4 0 00-2.81 1.19z"></path>
        </svg>
        <h3 class="text-lg font-medium text-gray-900 mb-2">No price alerts set</h3>
        <p class="text-gray-500 mb-4">Create your first price alert to get notified when cryptocurrencies reach your target price.</p>
        <button
          @click="showAddAlertModal = true"
          class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors"
        >
          Create Your First Alert
        </button>
      </div>

      <div v-else class="overflow-x-auto">
        <table class="w-full">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cryptocurrency</th>
              <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Condition</th>
              <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Target Price</th>
              <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Current Price</th>
              <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
              <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created</th>
              <th class="px-6 py-4 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <tr v-for="alert in alerts" :key="alert.id" class="hover:bg-gray-50">
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="flex items-center">
                  <img :src="alert.image" :alt="alert.name" class="w-8 h-8 rounded-full mr-3">
                  <div>
                    <div class="text-sm font-medium text-gray-900">{{ alert.name }}</div>
                    <div class="text-sm text-gray-500">{{ alert.symbol.toUpperCase() }}</div>
                  </div>
                </div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium" :class="alert.condition === 'above' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'">
                  {{ alert.condition === 'above' ? 'Above' : 'Below' }}
                </span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                {{ formatPrice(alert.targetPrice) }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                {{ formatPrice(alert.currentPrice) }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium" :class="alert.status === 'active' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'">
                  {{ alert.status === 'active' ? 'Active' : 'Triggered' }}
                </span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                {{ formatDate(alert.createdAt) }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                <button
                  @click="toggleAlert(alert.id)"
                  class="text-blue-600 hover:text-blue-800 mr-3"
                >
                  {{ alert.status === 'active' ? 'Pause' : 'Resume' }}
                </button>
                <button
                  @click="deleteAlert(alert.id)"
                  class="text-red-600 hover:text-red-800"
                >
                  Delete
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Add Alert Modal -->
    <div v-if="showAddAlertModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white rounded-xl p-6 w-full max-w-md mx-4">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Create Price Alert</h3>
        
        <div class="space-y-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Cryptocurrency</label>
            <select
              v-model="newAlert.cryptoId"
              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            >
              <option value="">Select a cryptocurrency</option>
              <option v-for="crypto in cryptocurrencies" :key="crypto.id" :value="crypto.id">
                {{ crypto.name }} ({{ crypto.symbol.toUpperCase() }})
              </option>
            </select>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Condition</label>
            <select
              v-model="newAlert.condition"
              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            >
              <option value="above">Price goes above</option>
              <option value="below">Price goes below</option>
            </select>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Target Price</label>
            <input
              v-model.number="newAlert.targetPrice"
              type="number"
              step="0.01"
              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              placeholder="0.00"
            >
          </div>

          <div class="flex items-center">
            <input
              v-model="newAlert.repeat"
              type="checkbox"
              class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
            >
            <label class="ml-2 block text-sm text-gray-900">
              Repeat alert
            </label>
          </div>
        </div>

        <div class="flex justify-end space-x-3 mt-6">
          <button
            @click="showAddAlertModal = false"
            class="px-4 py-2 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors"
          >
            Cancel
          </button>
          <button
            @click="addAlert"
            :disabled="!newAlert.cryptoId || !newAlert.targetPrice"
            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
          >
            Create Alert
          </button>
        </div>
      </div>
    </div>

    <!-- Notification Settings -->
    <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-200">
      <h3 class="text-lg font-semibold text-gray-900 mb-4">Notification Settings</h3>
      
      <div class="space-y-4">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-sm font-medium text-gray-900">Push Notifications</p>
            <p class="text-sm text-gray-500">Receive notifications when alerts are triggered</p>
          </div>
          <button
            @click="notificationsEnabled = !notificationsEnabled"
            class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors"
            :class="notificationsEnabled ? 'bg-blue-600' : 'bg-gray-200'"
          >
            <span
              class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform"
              :class="notificationsEnabled ? 'translate-x-6' : 'translate-x-1'"
            ></span>
          </button>
        </div>

        <div class="flex items-center justify-between">
          <div>
            <p class="text-sm font-medium text-gray-900">Email Notifications</p>
            <p class="text-sm text-gray-500">Receive email alerts for triggered price alerts</p>
          </div>
          <button
            @click="emailNotifications = !emailNotifications"
            class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors"
            :class="emailNotifications ? 'bg-blue-600' : 'bg-gray-200'"
          >
            <span
              class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform"
              :class="emailNotifications ? 'translate-x-6' : 'translate-x-1'"
            ></span>
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
const showAddAlertModal = ref(false)
const notificationsEnabled = ref(true)
const emailNotifications = ref(false)
const alerts = ref<any[]>([])
const newAlert = ref({
  cryptoId: '',
  condition: 'above',
  targetPrice: 0,
  repeat: false
})

// Computed properties
const activeAlerts = computed(() => 
  alerts.value.filter(alert => alert.status === 'active')
)

const triggeredToday = computed(() => {
  const today = new Date().toDateString()
  return alerts.value.filter(alert => 
    alert.status === 'triggered' && 
    new Date(alert.triggeredAt).toDateString() === today
  ).length
})

// Methods
const addAlert = () => {
  const crypto = cryptocurrencies.value.find(c => c.id === newAlert.value.cryptoId)
  if (!crypto) return

  const alert = {
    id: Date.now().toString(),
    cryptoId: newAlert.value.cryptoId,
    name: crypto.name,
    symbol: crypto.symbol,
    image: crypto.image,
    condition: newAlert.value.condition,
    targetPrice: newAlert.value.targetPrice,
    currentPrice: crypto.current_price,
    status: 'active',
    repeat: newAlert.value.repeat,
    createdAt: new Date(),
    triggeredAt: null
  }

  alerts.value.push(alert)
  showAddAlertModal.value = false
  
  // Reset form
  newAlert.value = {
    cryptoId: '',
    condition: 'above',
    targetPrice: 0,
    repeat: false
  }
}

const toggleAlert = (alertId: string) => {
  const alert = alerts.value.find(a => a.id === alertId)
  if (alert) {
    alert.status = alert.status === 'active' ? 'paused' : 'active'
  }
}

const deleteAlert = (alertId: string) => {
  alerts.value = alerts.value.filter(alert => alert.id !== alertId)
}

const formatDate = (date: Date) => {
  return new Intl.DateTimeFormat('en-US', {
    month: 'short',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  }).format(date)
}
</script>