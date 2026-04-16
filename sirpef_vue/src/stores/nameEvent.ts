import Http from '@/utils/Http';
import { defineStore } from 'pinia'

  
export const useEventsName = defineStore('eventsName', {
    state: () => ({
      id: '',
      name: '',
      subtitle: '',
      whitCortesia: false
    }),
    actions: {
        async GetUserInfo() {
            const response = await Http.get("/api/registro/user/info");
            
            const { data } = response;
            const firstItem = data.length > 0 ? data[0] : {};
            
            this.id = firstItem.id || '';
            this.name = firstItem.title || '';
            this.subtitle = firstItem.subtitle || '';
            this.whitCortesia = firstItem.cortesia || false;
          },

        async withEvent() {
            return this.id
        },
    }
  });