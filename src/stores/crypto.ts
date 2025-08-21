import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import axios from 'axios'

export interface CryptoCurrency {
  id: string
  symbol: string
  name: string
  image: string
  current_price: number
  market_cap: number
  market_cap_rank: number
  total_volume: number
  high_24h: number
  low_24h: number
  price_change_24h: number
  price_change_percentage_24h: number
  market_cap_change_24h: number
  market_cap_change_percentage_24h: number
  circulating_supply: number
  total_supply: number
  max_supply: number
  ath: number
  ath_change_percentage: number
  ath_date: string
  atl: number
  atl_change_percentage: number
  atl_date: string
  last_updated: string
}

export interface TelegramMessage {
  id: string
  text: string
  timestamp: Date
  type: 'user' | 'bot'
  cryptoData?: CryptoCurrency
}

export const useCryptoStore = defineStore('crypto', () => {
  const cryptocurrencies = ref<CryptoCurrency[]>([])
  const loading = ref(false)
  const error = ref<string | null>(null)
  const selectedCurrency = ref<CryptoCurrency | null>(null)
  const messages = ref<TelegramMessage[]>([])
  const userPreferences = ref({
    defaultCurrency: 'usd',
    refreshInterval: 30000, // 30 seconds
    notifications: true
  })

  // Computed properties
  const topGainers = computed(() => 
    [...cryptocurrencies.value]
      .sort((a, b) => b.price_change_percentage_24h - a.price_change_percentage_24h)
      .slice(0, 5)
  )

  const topLosers = computed(() => 
    [...cryptocurrencies.value]
      .sort((a, b) => a.price_change_percentage_24h - b.price_change_percentage_24h)
      .slice(0, 5)
  )

  const totalMarketCap = computed(() => 
    cryptocurrencies.value.reduce((sum, crypto) => sum + crypto.market_cap, 0)
  )

  // Actions
  const fetchCryptocurrencies = async () => {
    loading.value = true
    error.value = null
    
    try {
      const response = await axios.get(
        `https://api.coingecko.com/api/v3/coins/markets?vs_currency=${userPreferences.value.defaultCurrency}&order=market_cap_desc&per_page=100&page=1&sparkline=false&locale=en`
      )
      cryptocurrencies.value = response.data
    } catch (err) {
      error.value = 'Failed to fetch cryptocurrency data'
      console.error('Error fetching cryptocurrencies:', err)
    } finally {
      loading.value = false
    }
  }

  const fetchCryptoDetails = async (id: string) => {
    try {
      const response = await axios.get(
        `https://api.coingecko.com/api/v3/coins/${id}?localization=false&tickers=false&market_data=true&community_data=false&developer_data=false&sparkline=false`
      )
      return response.data
    } catch (err) {
      console.error('Error fetching crypto details:', err)
      return null
    }
  }

  const addMessage = (message: Omit<TelegramMessage, 'id'>) => {
    const newMessage: TelegramMessage = {
      ...message,
      id: Date.now().toString()
    }
    messages.value.push(newMessage)
  }

  const searchCryptocurrencies = (query: string) => {
    if (!query.trim()) return cryptocurrencies.value
    
    return cryptocurrencies.value.filter(crypto =>
      crypto.name.toLowerCase().includes(query.toLowerCase()) ||
      crypto.symbol.toLowerCase().includes(query.toLowerCase())
    )
  }

  const setSelectedCurrency = (crypto: CryptoCurrency) => {
    selectedCurrency.value = crypto
  }

  const updatePreferences = (preferences: Partial<typeof userPreferences.value>) => {
    userPreferences.value = { ...userPreferences.value, ...preferences }
  }

  const getPriceChangeColor = (percentage: number) => {
    return percentage >= 0 ? 'text-green-500' : 'text-red-500'
  }

  const formatPrice = (price: number) => {
    return new Intl.NumberFormat('en-US', {
      style: 'currency',
      currency: userPreferences.value.defaultCurrency.toUpperCase()
    }).format(price)
  }

  const formatMarketCap = (marketCap: number) => {
    if (marketCap >= 1e12) {
      return `$${(marketCap / 1e12).toFixed(2)}T`
    } else if (marketCap >= 1e9) {
      return `$${(marketCap / 1e9).toFixed(2)}B`
    } else if (marketCap >= 1e6) {
      return `$${(marketCap / 1e6).toFixed(2)}M`
    } else {
      return `$${marketCap.toLocaleString()}`
    }
  }

  return {
    // State
    cryptocurrencies,
    loading,
    error,
    selectedCurrency,
    messages,
    userPreferences,
    
    // Computed
    topGainers,
    topLosers,
    totalMarketCap,
    
    // Actions
    fetchCryptocurrencies,
    fetchCryptoDetails,
    addMessage,
    searchCryptocurrencies,
    setSelectedCurrency,
    updatePreferences,
    getPriceChangeColor,
    formatPrice,
    formatMarketCap
  }
})