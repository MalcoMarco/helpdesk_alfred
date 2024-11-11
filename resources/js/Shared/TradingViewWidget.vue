<template>
    <!-- TradingView Widget BEGIN -->
    <div class="tradingview-widget-container">
      <div class="tradingview-widget-container__widget"></div>
      <div class="tradingview-widget-copyright">
        <a href="https://es.tradingview.com/" rel="noopener nofollow" target="_blank">
          <span class="blue-text">alfredpay en online</span>
        </a>
      </div>
    </div>
    <!-- TradingView Widget END -->
  </template>
  
  <script>
  export default {
    name: 'TradingViewWidget',
    mounted() {
    const script = document.createElement('script');
      script.src = 'https://s3.tradingview.com/external-embedding/embed-widget-ticker-tape.js';
      script.async = true;
      script.innerHTML = JSON.stringify({
        symbols: [
          { proName: "FOREXCOM:SPXUSD", title: "S&P 500 Index" },
          { proName: "FOREXCOM:NSXUSD", title: "US 100 Cash CFD" },
          { proName: "FX_IDC:EURUSD", title: "EUR to USD" },
          { proName: "BITSTAMP:BTCUSD", title: "Bitcoin" },
          { proName: "BITSTAMP:ETHUSD", title: "Ethereum" },
          { description: "alfredpay® BITCOIN", proName: "COINBASE:BTCUSD" },
          { description: "alfredpay® USDC", proName: "CRYPTOCAP:USDC" },
          { description: "alfredpay® ET", proName: "FOREXCOM:ETHUSD" }
        ],
        showSymbolLogo: true,
        isTransparent: false,
        largeChartUrl: "https://helpdesk.alfred.com.do/dashboard",
        displayMode: "adaptive",
        colorTheme: "dark",
        locale: "es"
      });
      this.$nextTick(() => {
          
          document.body.appendChild(script);
          console.log('TradingViewWidget mounted');
      });
    },
  }
  </script>
  
  <style scoped>
  /* Añade estilos personalizados aquí si es necesario */
  </style>