import Vue from 'vue'
import App from './App.vue'
import router from './router'
import store from './store'
import vuetify from './plugins/vuetify'

import moment from 'moment';
import VueMoment from 'vue-moment';

// Load Locales ('en' comes loaded by default)
require('moment/locale/es');

// Choose Locale
moment.locale('es');

Vue.use(VueMoment, { moment });

/***************Configuracion fire base store**************************************** */
//import firebase from 'firebase/app'
//import 'firebase/firestore'

// Import the functions you need from the SDKs you need
//import { initializeApp } from "firebase/app";
//import { getFirestore } from 'firebase/firestore/lite';
// TODO: Add SDKs for Firebase products that you want to use
// https://firebase.google.com/docs/web/setup#available-libraries

// Your web app's Firebase configuration

/*
const firebaseConfig = {
  apiKey: "AIzaSyCnIZAb8oJDIvO_Lzc948YT5V2MnMBia9Q",
  authDomain: "vue-calendario-demo.firebaseapp.com",
  databaseURL: "https://vue-calendario-demo-default-rtdb.firebaseio.com",
  projectId: "vue-calendario-demo",
  storageBucket: "vue-calendario-demo.appspot.com",
  messagingSenderId: "291505296428",
  appId: "1:291505296428:web:721612e644a5fa0dbd6d89"
};

// Initialize Firebase
 const app = initializeApp(firebaseConfig);
export const db = getFirestore(app);
*/

//firebase.initializeApp(firebaseConfig);
//export const db = app.firestore();
/***************Configuracion fire base store**************************************** */

Vue.config.productionTip = false

new Vue({
  router,
  store,
  vuetify,
  
  render: h => h(App)
}).$mount('#app')
