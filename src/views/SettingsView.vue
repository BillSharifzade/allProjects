<template>
  <div class="p-6 space-y-6">
    <!-- Settings Header -->
    <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-200">
      <h1 class="text-2xl font-bold text-gray-900 mb-2">Settings</h1>
      <p class="text-gray-600">Customize your CryptoBot experience</p>
    </div>

    <!-- General Settings -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
      <div class="p-6 border-b border-gray-200">
        <h3 class="text-lg font-semibold text-gray-900">General Settings</h3>
      </div>
      <div class="p-6 space-y-6">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Default Currency</label>
          <select
            v-model="settings.defaultCurrency"
            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
          >
            <option value="usd">USD - US Dollar</option>
            <option value="eur">EUR - Euro</option>
            <option value="gbp">GBP - British Pound</option>
            <option value="jpy">JPY - Japanese Yen</option>
            <option value="cad">CAD - Canadian Dollar</option>
            <option value="aud">AUD - Australian Dollar</option>
          </select>
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Data Refresh Interval</label>
          <select
            v-model="settings.refreshInterval"
            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
          >
            <option value="10000">10 seconds</option>
            <option value="30000">30 seconds</option>
            <option value="60000">1 minute</option>
            <option value="300000">5 minutes</option>
            <option value="600000">10 minutes</option>
          </select>
          <p class="text-sm text-gray-500 mt-1">How often to refresh cryptocurrency data</p>
        </div>

        <div class="flex items-center justify-between">
          <div>
            <p class="text-sm font-medium text-gray-900">Auto-refresh</p>
            <p class="text-sm text-gray-500">Automatically refresh data at the specified interval</p>
          </div>
          <button
            @click="settings.autoRefresh = !settings.autoRefresh"
            class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors"
            :class="settings.autoRefresh ? 'bg-blue-600' : 'bg-gray-200'"
          >
            <span
              class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform"
              :class="settings.autoRefresh ? 'translate-x-6' : 'translate-x-1'"
            ></span>
          </button>
        </div>
      </div>
    </div>

    <!-- Display Settings -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
      <div class="p-6 border-b border-gray-200">
        <h3 class="text-lg font-semibold text-gray-900">Display Settings</h3>
      </div>
      <div class="p-6 space-y-6">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Theme</label>
          <div class="grid grid-cols-3 gap-3">
            <button
              @click="settings.theme = 'light'"
              class="p-4 border-2 rounded-lg text-center transition-colors"
              :class="settings.theme === 'light' ? 'border-blue-500 bg-blue-50' : 'border-gray-200 hover:border-gray-300'"
            >
              <svg class="w-8 h-8 mx-auto mb-2 text-yellow-500" fill="currentColor" viewBox="0 0 24 24">
                <path d="M12 2.25a.75.75 0 01.75.75v2.25a.75.75 0 01-1.5 0V3a.75.75 0 01.75-.75zM7.5 12a4.5 4.5 0 119 0 4.5 4.5 0 01-9 0zM18.894 6.166a.75.75 0 00-1.06-1.06l-1.591 1.59a.75.75 0 101.06 1.061l1.591-1.59zM21.75 12a.75.75 0 01-.75.75h-2.25a.75.75 0 010-1.5H21a.75.75 0 01.75.75zM17.834 18.894a.75.75 0 001.06-1.06l-1.59-1.591a.75.75 0 10-1.061 1.06l1.59 1.591zM12 18a.75.75 0 01.75.75V21a.75.75 0 01-1.5 0v-2.25A.75.75 0 0112 18zM7.758 17.303a.75.75 0 00-1.061-1.06l-1.591 1.59a.75.75 0 001.06 1.061l1.591-1.59zM6 12a.75.75 0 01-.75.75H3a.75.75 0 010-1.5h2.25A.75.75 0 016 12zM6.697 7.757a.75.75 0 001.06-1.06l-1.59-1.591a.75.75 0 00-1.061 1.06l1.59 1.591z"/>
              </svg>
              <span class="text-sm font-medium">Light</span>
            </button>
            <button
              @click="settings.theme = 'dark'"
              class="p-4 border-2 rounded-lg text-center transition-colors"
              :class="settings.theme === 'dark' ? 'border-blue-500 bg-blue-50' : 'border-gray-200 hover:border-gray-300'"
            >
              <svg class="w-8 h-8 mx-auto mb-2 text-gray-700" fill="currentColor" viewBox="0 0 24 24">
                <path d="M9.528 1.718a.75.75 0 01.162.819A8.97 8.97 0 009 6a9 9 0 009 9 8.97 8.97 0 003.463-.69.75.75 0 01.981.98 10.503 10.503 0 01-9.694 6.46c-5.799 0-10.5-4.701-10.5-10.5 0-4.368 2.667-8.112 6.46-9.694a.75.75 0 01.818.162z"/>
              </svg>
              <span class="text-sm font-medium">Dark</span>
            </button>
            <button
              @click="settings.theme = 'auto'"
              class="p-4 border-2 rounded-lg text-center transition-colors"
              :class="settings.theme === 'auto' ? 'border-blue-500 bg-blue-50' : 'border-gray-200 hover:border-gray-300'"
            >
              <svg class="w-8 h-8 mx-auto mb-2 text-gray-600" fill="currentColor" viewBox="0 0 24 24">
                <path d="M12 2.25a.75.75 0 01.75.75v2.25a.75.75 0 01-1.5 0V3a.75.75 0 01.75-.75zM7.5 12a4.5 4.5 0 119 0 4.5 4.5 0 01-9 0zM18.894 6.166a.75.75 0 00-1.06-1.06l-1.591 1.59a.75.75 0 101.06 1.061l1.591-1.59zM21.75 12a.75.75 0 01-.75.75h-2.25a.75.75 0 010-1.5H21a.75.75 0 01.75.75zM17.834 18.894a.75.75 0 001.06-1.06l-1.59-1.591a.75.75 0 10-1.061 1.06l1.59 1.591zM12 18a.75.75 0 01.75.75V21a.75.75 0 01-1.5 0v-2.25A.75.75 0 0112 18zM7.758 17.303a.75.75 0 00-1.061-1.06l-1.591 1.59a.75.75 0 001.06 1.061l1.591-1.59zM6 12a.75.75 0 01-.75.75H3a.75.75 0 010-1.5h2.25A.75.75 0 016 12zM6.697 7.757a.75.75 0 001.06-1.06l-1.59-1.591a.75.75 0 00-1.061 1.06l1.59 1.591z"/>
              </svg>
              <span class="text-sm font-medium">Auto</span>
            </button>
          </div>
        </div>

        <div class="flex items-center justify-between">
          <div>
            <p class="text-sm font-medium text-gray-900">Compact Mode</p>
            <p class="text-sm text-gray-500">Show more data in less space</p>
          </div>
          <button
            @click="settings.compactMode = !settings.compactMode"
            class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors"
            :class="settings.compactMode ? 'bg-blue-600' : 'bg-gray-200'"
          >
            <span
              class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform"
              :class="settings.compactMode ? 'translate-x-6' : 'translate-x-1'"
            ></span>
          </button>
        </div>

        <div class="flex items-center justify-between">
          <div>
            <p class="text-sm font-medium text-gray-900">Show Price Changes</p>
            <p class="text-sm text-gray-500">Display 24h price change percentages</p>
          </div>
          <button
            @click="settings.showPriceChanges = !settings.showPriceChanges"
            class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors"
            :class="settings.showPriceChanges ? 'bg-blue-600' : 'bg-gray-200'"
          >
            <span
              class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform"
              :class="settings.showPriceChanges ? 'translate-x-6' : 'translate-x-1'"
            ></span>
          </button>
        </div>
      </div>
    </div>

    <!-- Notification Settings -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
      <div class="p-6 border-b border-gray-200">
        <h3 class="text-lg font-semibold text-gray-900">Notifications</h3>
      </div>
      <div class="p-6 space-y-6">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-sm font-medium text-gray-900">Push Notifications</p>
            <p class="text-sm text-gray-500">Receive browser notifications</p>
          </div>
          <button
            @click="settings.pushNotifications = !settings.pushNotifications"
            class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors"
            :class="settings.pushNotifications ? 'bg-blue-600' : 'bg-gray-200'"
          >
            <span
              class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform"
              :class="settings.pushNotifications ? 'translate-x-6' : 'translate-x-1'"
            ></span>
          </button>
        </div>

        <div class="flex items-center justify-between">
          <div>
            <p class="text-sm font-medium text-gray-900">Sound Alerts</p>
            <p class="text-sm text-gray-500">Play sound when alerts are triggered</p>
          </div>
          <button
            @click="settings.soundAlerts = !settings.soundAlerts"
            class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors"
            :class="settings.soundAlerts ? 'bg-blue-600' : 'bg-gray-200'"
          >
            <span
              class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform"
              :class="settings.soundAlerts ? 'translate-x-6' : 'translate-x-1'"
            ></span>
          </button>
        </div>

        <div class="flex items-center justify-between">
          <div>
            <p class="text-sm font-medium text-gray-900">Email Notifications</p>
            <p class="text-sm text-gray-500">Send email alerts for price changes</p>
          </div>
          <button
            @click="settings.emailNotifications = !settings.emailNotifications"
            class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors"
            :class="settings.emailNotifications ? 'bg-blue-600' : 'bg-gray-200'"
          >
            <span
              class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform"
              :class="settings.emailNotifications ? 'translate-x-6' : 'translate-x-1'"
            ></span>
          </button>
        </div>
      </div>
    </div>

    <!-- Data & Privacy -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
      <div class="p-6 border-b border-gray-200">
        <h3 class="text-lg font-semibold text-gray-900">Data & Privacy</h3>
      </div>
      <div class="p-6 space-y-6">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-sm font-medium text-gray-900">Analytics</p>
            <p class="text-sm text-gray-500">Help improve CryptoBot with anonymous usage data</p>
          </div>
          <button
            @click="settings.analytics = !settings.analytics"
            class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors"
            :class="settings.analytics ? 'bg-blue-600' : 'bg-gray-200'"
          >
            <span
              class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform"
              :class="settings.analytics ? 'translate-x-6' : 'translate-x-1'"
            ></span>
          </button>
        </div>

        <div class="flex items-center justify-between">
          <div>
            <p class="text-sm font-medium text-gray-900">Local Storage</p>
            <p class="text-sm text-gray-500">Store portfolio and settings locally</p>
          </div>
          <button
            @click="settings.localStorage = !settings.localStorage"
            class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors"
            :class="settings.localStorage ? 'bg-blue-600' : 'bg-gray-200'"
          >
            <span
              class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform"
              :class="settings.localStorage ? 'translate-x-6' : 'translate-x-1'"
            ></span>
          </button>
        </div>

        <div class="pt-4 border-t border-gray-200">
          <button
            @click="clearData"
            class="text-red-600 hover:text-red-800 text-sm font-medium"
          >
            Clear All Data
          </button>
          <p class="text-xs text-gray-500 mt-1">This will remove all your portfolio data and settings</p>
        </div>
      </div>
    </div>

    <!-- About -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
      <div class="p-6 border-b border-gray-200">
        <h3 class="text-lg font-semibold text-gray-900">About</h3>
      </div>
      <div class="p-6 space-y-4">
        <div class="flex items-center justify-between">
          <span class="text-sm text-gray-600">Version</span>
          <span class="text-sm font-medium text-gray-900">1.0.0</span>
        </div>
        <div class="flex items-center justify-between">
          <span class="text-sm text-gray-600">Data Source</span>
          <span class="text-sm font-medium text-gray-900">CoinGecko API</span>
        </div>
        <div class="flex items-center justify-between">
          <span class="text-sm text-gray-600">Last Updated</span>
          <span class="text-sm font-medium text-gray-900">{{ lastUpdated }}</span>
        </div>
        
        <div class="pt-4 border-t border-gray-200">
          <div class="flex space-x-4">
            <a href="#" class="text-blue-600 hover:text-blue-800 text-sm font-medium">Privacy Policy</a>
            <a href="#" class="text-blue-600 hover:text-blue-800 text-sm font-medium">Terms of Service</a>
            <a href="#" class="text-blue-600 hover:text-blue-800 text-sm font-medium">Support</a>
          </div>
        </div>
      </div>
    </div>

    <!-- Save Button -->
    <div class="flex justify-end">
      <button
        @click="saveSettings"
        class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium"
      >
        Save Settings
      </button>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue'
import { useCryptoStore } from '@/stores/crypto'

const cryptoStore = useCryptoStore()
const { updatePreferences } = cryptoStore

// Local state
const settings = ref({
  defaultCurrency: 'usd',
  refreshInterval: 30000,
  autoRefresh: true,
  theme: 'light',
  compactMode: false,
  showPriceChanges: true,
  pushNotifications: true,
  soundAlerts: false,
  emailNotifications: false,
  analytics: true,
  localStorage: true
})

const lastUpdated = computed(() => {
  return new Date().toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
})

// Methods
const saveSettings = () => {
  updatePreferences({
    defaultCurrency: settings.value.defaultCurrency,
    refreshInterval: settings.value.refreshInterval
  })
  
  // Save to localStorage
  localStorage.setItem('cryptobot-settings', JSON.stringify(settings.value))
  
  // Show success message (you could add a toast notification here)
  console.log('Settings saved successfully')
}

const clearData = () => {
  if (confirm('Are you sure you want to clear all data? This action cannot be undone.')) {
    localStorage.clear()
    // Reset settings to defaults
    settings.value = {
      defaultCurrency: 'usd',
      refreshInterval: 30000,
      autoRefresh: true,
      theme: 'light',
      compactMode: false,
      showPriceChanges: true,
      pushNotifications: true,
      soundAlerts: false,
      emailNotifications: false,
      analytics: true,
      localStorage: true
    }
  }
}

// Load settings from localStorage on mount
const loadSettings = () => {
  const savedSettings = localStorage.getItem('cryptobot-settings')
  if (savedSettings) {
    try {
      const parsed = JSON.parse(savedSettings)
      settings.value = { ...settings.value, ...parsed }
    } catch (error) {
      console.error('Failed to load settings:', error)
    }
  }
}

// Load settings when component mounts
loadSettings()
</script>