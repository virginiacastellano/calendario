<template>
  <v-row class="fill-height">
    <v-col>
      <v-sheet height="64">
        <v-toolbar
          flat
        >
        <v-btn dark class="mr-4" color="primary" @click="dialog = true">
            Agregar
          </v-btn>

          <v-btn outlined class="mr-4" color="grey darken-2" @click="setToday">
            Hoy
          </v-btn>
          <v-btn
            fab
            text
            small
            color="grey darken-2"
            @click="prev"
          >
            <v-icon small>
              mdi-chevron-left
            </v-icon>
          </v-btn>
          <v-btn
            fab
            text
            small
            color="grey darken-2"
            @click="next"
          >
            <v-icon small>
              mdi-chevron-right
            </v-icon>
          </v-btn>
          <v-toolbar-title v-if="$refs.calendar">
            {{ $refs.calendar.title }}
          </v-toolbar-title>
          <v-spacer></v-spacer>
          <v-menu
            bottom
            right
          >
            <template v-slot:activator="{ on, attrs }">
              <v-btn
                outlined
                color="grey darken-2"
                v-bind="attrs"
                v-on="on"
              >
                <span>{{ typeToLabel[type] }}</span>
                <v-icon right>
                  mdi-menu-down
                </v-icon>
              </v-btn>
            </template>
            <v-list>
              <v-list-item @click="type = 'day'">
                <v-list-item-title>Día</v-list-item-title>
              </v-list-item>
              <v-list-item @click="type = 'week'">
                <v-list-item-title>Semana</v-list-item-title>
              </v-list-item>
              <v-list-item @click="type = 'month'">
                <v-list-item-title>Mes</v-list-item-title>
              </v-list-item>
              <v-list-item @click="type = '4day'">
                <v-list-item-title>4 Días</v-list-item-title>
              </v-list-item>
            </v-list>
          </v-menu>
        </v-toolbar>
      </v-sheet>
      <v-sheet height="600">
        <v-calendar
          ref="calendar"
          v-model="focus"
          color="primary"
          :events="events"
          :event-color="getEventColor"
          :type="type"
          @click:event="showEvent"
          @click:more="viewDay"
          @click:date="viewDay"
          @change="updateRange"
         :weekdays="weekdays"
          locale="es"
          :short-weekdays="false"
        ></v-calendar>


<!-- Modal Agregar Evento -->
        <v-dialog v-model="dialog">
          <v-card>
            <v-container>
              <v-form @submit.prevent="addEvent">
                <v-text-field 
                  type="text" label="Agregar Nombre" v-model="name">
                </v-text-field>
                <v-text-field 
                  type="text" label="Agregar un Detalle" v-model="details">
                </v-text-field>
                <v-text-field 
                  type="datetime-local" label="Inicio del evento" v-model="start">
                </v-text-field>
                <v-text-field 
                  type="datetime-local" label="Fin del evento" v-model="end">
                </v-text-field>
                
                <v-text-field 
                  type="color" label="Color del evento" v-model="color">
                </v-text-field>
                <v-btn type="submit" color="primary" class="mr-4" 
                @click.stop="dialog = false">Agregar</v-btn>
              </v-form>
            </v-container>
          </v-card>
        </v-dialog>

        <v-menu
          v-model="selectedOpen"
          :close-on-content-click="false"
          :activator="selectedElement"
          offset-x
        >
          <v-card
            color="grey lighten-4"
            min-width="350px"
            flat
          >
            <v-toolbar
              :color="selectedEvent.color"
              dark
            >
              <v-btn icon @click="deleteEvent(selectedEvent)">
                <v-icon>mdi-delete</v-icon>
              </v-btn>
              <v-toolbar-title v-html="selectedEvent.name"></v-toolbar-title>
              <v-spacer></v-spacer>
            </v-toolbar>
            <v-card-text>

              <!--span v-html="selectedEvent.details"></span-->
              <v-form v-if="currentlyEditing !== selectedEvent.id">
                
                  <span v-html="selectedEvent.details"></span>
              </v-form>
              <v-form v-else>

                <v-text-field 
                  type="text" label="Editar Nombre" v-model="selectedEvent.name">
                </v-text-field>
                <v-text-field 
                  type="text" label="Editar Detalle" v-model="selectedEvent.details">
                </v-text-field>
        
                <v-text-field 
                  type="datetime-local" label="Inicio del evento"   v-model="selectedEvent.start">
                </v-text-field>
                <v-text-field 
                  type="datetime-local" label="Fin del evento"   v-model="selectedEvent.end">
                </v-text-field>

                <v-text-field 
                  type="color" label="Color del evento" v-model="selectedEvent.color">
                </v-text-field>

              </v-form>
            </v-card-text>

            <v-card-actions>
              <v-btn
                text
                color="secondary"
                @click="selectedOpen = false"
              >
                Cancel
              </v-btn>
              <v-btn text v-if="currentlyEditing !== selectedEvent.id"
              @click.prevent="editEvent(selectedEvent.id)">Editar </v-btn>

               <v-btn text v-else  @click.prevent="updateEvent(selectedEvent)">Guardar Cambios</v-btn>
            </v-card-actions>
          </v-card>
        </v-menu>
      </v-sheet>
    </v-col>
  </v-row>
</template>


<script>
//import {db} from '../main'
//import { collection, getDocs } from 'firebase/firestore/lite';
//import { collection, getDocs,doc, setDoc } from "firebase/firestore";

  export default {
    data: () => ({
      selectedEventStart:null,
      selectedEventEnd:null,
       name: null,
      details: null,
      color: '#1976D2',
      dialog: false,
      currentlyEditing: null,
      focus: '',
      type: 'month',
      typeToLabel: {
        month: 'Mes',
        week: 'Semana',
        day: 'Día',
        '4day': '4 Días',
      },
      weekdays: [1, 2, 3, 4, 5, 6, 0],
        start: null,
      end: null,
      selectedEvent: {},
      selectedElement: null,
      selectedOpen: false,
      events: [],
      colors: ['#3f51b5', '#3f51b5', '#673ab7', '#00bcd4', '#4caf50', '#ff9800', '#757575'],
      names: ['Meeting', 'Holiday', 'PTO', 'Travel', 'Event', 'Birthday', 'Conference', 'Party'],
    }),
    mounted () {
      this.$refs.calendar.checkChange();
      
    },
    created(){
       // this.getEvents();
    },
    methods: {
      /*formateameLaFechaStart(fecha){
        //alert(fecha)
        this.selectedEventStart = this.$moment(fecha).format('YYYY-MM-DDThh:mm');
        return this.$moment(fecha).format('YYYY-MM-DDThh:mm');//this.fechaFormat;
      },
      formateameLaFechaEnd(fecha){
        //alert(fecha)
        this.selectedEventEnd = this.$moment(fecha).format('YYYY-MM-DDThh:mm');
        return this.$moment(fecha).format('YYYY-MM-DDThh:mm');//this.fechaFormat;
      },*/
      viewDay ({ date }) {
        this.focus = date
        this.type = 'day'
      },
      getEventColor (event) {
        return event.color
      },
      setToday () {
        this.focus = ''
      },
      prev () {
        this.$refs.calendar.prev()
      },
      next () {
        this.$refs.calendar.next()
      },
      showEvent ({ nativeEvent, event }) {
        const open = () => {
          this.selectedEvent = event
          this.selectedElement = nativeEvent.target
          requestAnimationFrame(() => requestAnimationFrame(() => this.selectedOpen = true))
        }

        if (this.selectedOpen) {
          this.selectedOpen = false
          requestAnimationFrame(() => requestAnimationFrame(() => open()))
        } else {
          open()
        }

        nativeEvent.stopPropagation()
      },
      updateRange ({ start, end }) {
        
        if(this.events.length == 0)
        {
          const events = []

          const min = new Date(`${start.date}T00:00:00`)
          const max = new Date(`${end.date}T23:59:59`)
          const days = (max.getTime() - min.getTime()) / 86400000
          const eventCount = this.rnd(days, days + 20)

          for (let i = 1; i < eventCount; i++) {
            const allDay = this.rnd(0, 3) === 0
            const firstTimestamp = this.rnd(min.getTime(), max.getTime())
            const first = new Date(firstTimestamp - (firstTimestamp % 900000))
            const secondTimestamp = this.rnd(2, allDay ? 288 : 8) * 900000
            const second = new Date(first.getTime() + secondTimestamp)
            const detalle = "<div>Este es mi id: "+i+"</div><div>Este es mi detalle</div><br> <div>TimeStart:"+this.$moment(first).format('YYYY-MM-DDThh:mm')+"</div><br> <div>TimeEnd:"+this.$moment(second).format('YYYY-MM-DDThh:mm')+"</div><br>";
            events.push({
              id:i,
              name: this.names[this.rnd(0, this.names.length - 1)],
              start: this.$moment(first).format('YYYY-MM-DDThh:mm'),
              details: detalle,
              end: this.$moment(second).format('YYYY-MM-DDThh:mm'),
              color: this.colors[this.rnd(0, this.colors.length - 1)],
              timed: !allDay,
            })
          }

          this.events = events
        }
        
        console.log("this.events: -> ",this.events)
        
      },
      rnd (a, b) {
        return Math.floor((b - a + 1) * Math.random()) + a
      },
      async getEvents(){
          try {

              /* const eventosCol = collection(db, 'eventos');
               const eventosSnapshot = await getDocs(eventosCol);
               const eventosList = eventosSnapshot.docs.map(doc => doc.data());*/
               /*console.log(eventosList)
                    eventosList.forEach(doc => {
                            console.log(doc);
                  
                    });*/
               // this.events = eventosList;
          } catch (error) {
              console.log(error)
              
          }
      },
      async addEvent(){
        try {
          if(this.name && this.start && this.end){
            /*
              // Add a new document with a generated id
              const newEventoRef = doc(collection(db, "eventos"));
              // later...
              await setDoc(newEventoRef, {
                name: this.name,
                details: this.details,
                start: this.start,
                end: this.end,
                color: this.color
              });
            */
            /* const res=  await collection(db,'eventos').add({
              name: this.name,
              details: this.details,
              start: this.start,
              end: this.end,
              color: this.color
            });*/
            const miID =this.events.length +1;
            this.events.push({
                id:miID,
                name: this.name,
                details: this.details,
                start: this.start,
                end: this.end,
                color: this.color,
                timed: !true,
            })
            //this.getEvents();
            this.name = null;
            this.details = null;
            this.start = null;
            this.end = null;
            this.color = '#1976D2';

          }else{
            console.log('Campos obligatorios');
          }
        } catch (error) {
          console.log(error);
        }
      },
      
       updateEvent(ev){
        try {

         /* await db.collection('eventos').doc(ev.id).update({
            name: ev.name,
            details: ev.details
          })*/
          /*this.events = this.events.filter(i=>i.id != ev.id)
          this.events.push({
                id:ev.id,
                name: ev.name,
                details: ev.details,
                start: ev.start,
                end: ev.end,
                color: ev.color,
                timed: !true,
            })*/
           /* ev.start =this.selectedEventStart;
            ev.end = this.selectedEventEnd;
           const index = this.events.findIndex(item => {return (ev.id=== item.id)})
           //this.events[index] = ev;
              this.events[index].id=ev.id;
                  this.events[index].name= ev.name;
                  this.events[index].details= ev.details;
                  this.events[index].start= ev.start;
                  this.events[index].end= ev.end;
                  this.events[index].color= ev.color;
                  this.events[index].timed= !true,
           */
           //this.selectedEventStart= null;
           //this.selectedEventEnd= null;

          this.selectedOpen = false;
          this.currentlyEditing = null;

        } catch (error) {
          console.log(error);
        }
      },
      editEvent(id){
        this.currentlyEditing = id
      },
      async deleteEvent(ev){
        try {
          
          //await db.collection('eventos').doc(ev.id).delete();

           //this.events.splice(ev);
          this.events = this.events.filter(i=>i.id != ev.id)
          this.selectedOpen = false;
          //this.getEvents();
          
        } catch (error) {
          console.log("deleteEvent",error);
        }
      },
      
    },
  }
</script>
