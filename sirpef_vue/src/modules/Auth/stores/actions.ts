// @ts-nocheck
import router from "@/router";
import * as AuthService from "@/modules/Auth/services";
import { getError } from "@/utils/helpers";

export default {
  logout() {
    localStorage.removeItem('SIRPEF-TOKEN')
    this.user = null;
    this.setGuest({ value: "isGuest" });    
    if (router.currentRoute.name !== "login")
      location.href = `${import.meta.env.VITE_SVA_URL}/dashboard`;
  return
    return AuthService.logout()    
      .then(() => {
        localStorage.removeItem('SIRPEF-TOKEN')
        this.user = null;
        this.setGuest({ value: "isGuest" });                    
        if (router.currentRoute.name !== "login")
          router.push({ path: "/" });
      })
      .catch((err) => {                  
        this.error = getError(err);
      });
  },
  async getAuthUser() {
    this.loading = true;
    try {
      const response = await AuthService.getAuthUser();        
      this.user = response.data;        
      this.loading = false;
      return response.data;
    } catch (err) {
      this.loading = false;        
      this.user = null;        
      this.error = getError(err);        
    }
  },
  setGuest({ value }: { value: string}) {  
    window.localStorage.setItem("guest", value);
  }
}
