<template>
  <v-app>


 <Calendario></Calendario>
  </v-app>
</template>

<script>
import Calendario from  './components/Calendario'

export default {
  name: 'App',
  components:{
    Calendario
    
  },
  data: () => ({
    //
  }),
};
</script>
