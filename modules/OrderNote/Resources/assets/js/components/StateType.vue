<template>
  <div>{{ state }}</div>
</template>

<script>
import {mapActions, mapState} from "vuex"

export default {
  props: ['id','description'],
  data() {
    return {
      resource: 'order-notes',
      state: '',
      options: [
        {id:'01', description: 'Pendiente'},
        {id:'03', description: 'Por Entregar'},
        {id:'05', description: 'Entregado'},
        {id:'11', description: 'Anulado'}
      ]
    }
  },
  created() {
    this.$store.commit('setConfiguration', this.configuration)
    this.loadConfiguration()
  },
  mounted(){
    this.setDescription()
  },
  computed:{
    ...mapState([
        'config',
    ]),
  },
  methods: {
    ...mapActions([
      'loadConfiguration',
    ]),
    setDescription(){
      if(this.config.order_node_advanced){
        this.state = this.options.find(e => e.id = this.id).description
      } else {
        this.state = this.description
      }
    }
  },
}
</script>

<style>

</style>