# CryptoBot - Cryptocurrency Telegram App

A modern, responsive cryptocurrency tracking application built with Vue.js 3, TypeScript, and Tailwind CSS. This app provides real-time cryptocurrency data, portfolio management, price alerts, and a Telegram-like interface.

## 🚀 Features

### 📊 Dashboard
- Real-time cryptocurrency market overview
- Top gainers and losers tracking
- Market sentiment analysis
- Total market cap and volume statistics
- Recent market activity feed

### 📈 Market View
- Comprehensive cryptocurrency listing
- Advanced search and filtering
- Sortable columns (market cap, price, volume, change)
- Pagination for better performance
- Real-time price updates

### 💼 Portfolio Management
- Track your cryptocurrency holdings
- Add/remove assets with quantity and average price
- Real-time profit/loss calculations
- Portfolio performance metrics
- 24h change tracking

### 🔔 Price Alerts
- Set custom price alerts for any cryptocurrency
- Above/below price conditions
- Repeatable alerts
- Push notifications support
- Email notifications (configurable)

### ⚙️ Settings & Customization
- Multiple currency support (USD, EUR, GBP, JPY, CAD, AUD)
- Customizable refresh intervals
- Theme selection (Light, Dark, Auto)
- Notification preferences
- Data privacy controls

## 🛠️ Technology Stack

- **Frontend Framework**: Vue.js 3 with Composition API
- **Language**: TypeScript
- **Styling**: Tailwind CSS
- **State Management**: Pinia
- **Routing**: Vue Router 4
- **HTTP Client**: Axios
- **Icons**: Heroicons
- **Data Source**: CoinGecko API

## 📦 Installation

1. **Clone the repository**
   ```bash
   git clone <repository-url>
   cd crypto-telegram-app
   ```

2. **Install dependencies**
   ```bash
   npm install
   ```

3. **Start the development server**
   ```bash
   npm run dev
   ```

4. **Open your browser**
   Navigate to `http://localhost:5173`

## 🏗️ Project Structure

```
src/
├── assets/          # Static assets and global styles
├── components/      # Reusable Vue components
├── router/          # Vue Router configuration
├── stores/          # Pinia stores for state management
├── views/           # Page components
│   ├── DashboardView.vue
│   ├── MarketView.vue
│   ├── PortfolioView.vue
│   ├── AlertsView.vue
│   └── SettingsView.vue
├── App.vue          # Root component
└── main.ts          # Application entry point
```

## 🔧 Configuration

### Environment Variables
Create a `.env` file in the root directory:

```env
VITE_API_BASE_URL=https://api.coingecko.com/api/v3
VITE_APP_TITLE=CryptoBot
```

### API Configuration
The app uses the CoinGecko API for cryptocurrency data. No API key is required for basic usage, but rate limits apply.

## 📱 Features in Detail

### Real-time Data
- Automatic data refresh every 30 seconds (configurable)
- Live price updates
- Market cap and volume tracking
- 24h price change percentages

### Portfolio Tracking
- Add multiple cryptocurrencies to your portfolio
- Track purchase price and quantity
- Real-time profit/loss calculations
- Portfolio performance metrics

### Price Alerts
- Set alerts for price movements above or below target
- Configurable notification methods
- Alert history and management
- Repeatable alerts for ongoing monitoring

### User Experience
- Responsive design for all devices
- Intuitive Telegram-like interface
- Fast loading and smooth animations
- Accessibility features

## 🚀 Deployment

### Build for Production
```bash
npm run build
```

### Preview Production Build
```bash
npm run preview
```

### Deploy to Vercel
```bash
npm install -g vercel
vercel
```

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add some amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

## 📄 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## 🙏 Acknowledgments

- [CoinGecko](https://coingecko.com/) for providing the cryptocurrency data API
- [Vue.js](https://vuejs.org/) team for the amazing framework
- [Tailwind CSS](https://tailwindcss.com/) for the utility-first CSS framework
- [Heroicons](https://heroicons.com/) for the beautiful icons

## 📞 Support

If you have any questions or need help, please open an issue on GitHub or contact the development team.

---

**Note**: This is a demo application for educational purposes. Always do your own research before making any investment decisions.