import { defineStore } from 'pinia'

  
export const useNotifications = defineStore('notify', {
    state: () => ({
      notifications: [], 
      count: 0
    }),
    actions: {
        loadNotis(){
            this.notifications = localStorage.getItem("notis") ? JSON.parse(localStorage.getItem("notis")) : []
        },

        resetCount() {
          this.count = 0;
        },
      addItem(noti) {
        this.count ++ 
        this.loadNotis()
        this.notifications.push(noti)
        localStorage.setItem("notis", JSON.stringify(this.notifications.slice(0, 20)))
      },
      removeItem(noti) {
          const notis = JSON.parse(localStorage.getItem("notis"))
          const notifs =  notis.filter(element => element != noti).reverse()
          this.notifications = notifs.slice(0, 20);
          //this.count = this.notifications.length
          localStorage.setItem("notis", JSON.stringify(this.notifications))      
      },
    }
  });