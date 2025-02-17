import { Map } from "./map.js";

let map = new Map({ id: id, lat: location_self[0], lng: location_self[1], user_type: 'passenger' });

class Main {
  constructor() {
    this.socket = new WebSocket(`ws://localhost:8080/chat?id=${id}&user_type=passenger`);

    this.socket.onopen = (event) => this.onopen(event);
    this.socket.onmessage = (event) => this.onmessage(event);
    this.socket.onerror = (event) => this.onerror(event);
    this.socket.onclose = (event) => this.onclose(event);

    this.update_var = false;
    this.rider_nearby = [];
    this.nearest_rider = null;
    this.ride_request = false;
    this.cycliv_update_var = null;
  }

  // ***Socket functions***
  close() {
    this.socket.close();
  }

  onopen(event) {
    console.log("WebSocket connection established:", event);
  }

  onmessage(event) {
    console.log("Message from server:", event.data);
    if(this.ride_request) {
      let data = JSON.parse(event.data);
      let repeat = false;
      this.rider_nearby.forEach((i) => {
        console.log(data.id);
        if(this.rider_nearby[i].id == data.id) repeat = true;
      });

      if(!repeat) {
        this.rider_nearby.push(data);
        this.update();
      }
    }
  }

  onerror(event) {
    console.error("WebSocket error:", event);
  }

  onclose(event) {
    console.log("WebSocket connection closed:", event);
  }

  /**
   * Sets the ditance or resets the notifcation bar
   * @param {number | false} min_distance - calculated minimum distance in km or false for reseting label.
   * @returns return null if distance is given false
   */
  setMinDistance(min_distance) {
    if (min_distance === false || this.nearest_rider === null) {
      document.querySelector(".notification-bar").innerHTML = `
      Welcome to 
      Ride<span style="font-family:'Dancing Script', serif;">Share</span>,
      Passenger`;

      map.clearPath();
      return;
    }

    if (min_distance > 1000) {
      min_distance /= 1000;
      min_distance = Number(min_distance.toFixed(1));
      min_distance = min_distance + "km";
    } else {
      min_distance *= 1000;
      min_distance = Number(min_distance.toFixed(0));
      min_distance = min_distance + "m";
    }

    document.querySelector(
      ".notification-bar"
    ).innerHTML = `A rider is ${min_distance} away from you`;

    map.clearPath();
    map.setPath(map.location_self, this.nearest_rider);
  }

  /**
   * This function sets the passenger markers on the map
   * @param {number} threshold - maximum number of passengers shown
   */
  setRiderOnMap(threshold = 4) {
    for (let i = 0; i < this.rider_nearby.length; i++) {
      map.markUserOnMap(this.rider_nearby[i], "rider", true);
    }
  }

  /**
   * this functions start/ends the repeated upadtion of user locations and information on map after a time-period
   * @param {boolean} toggle - defines weather to stop or start a function.
   */
  update(toggle = true) {
    // location update
    if (this.update_var === false && toggle == true) {
      map.markUserOnMap(map.location_self, "passenger", false, true);
      this.setRiderOnMap();

      // nearest user location update
      let min_distance;
      if (this.rider_nearby.length > 0) {
        min_distance = map.calculateDistance(
          map.location_self.lat,
          map.location_self.lng,
          this.rider_nearby[0].lat,
          this.rider_nearby[0].lng
        );
        this.nearest_rider = this.rider_nearby[0];
      }

      for (let i = 1; i < this.rider_nearby.length; i++) {
        let distance = map.calculateDistance(
          map.location_self.lat,
          map.location_self.lng,
          this.rider_nearby[i].lat,
          this.rider_nearby[i].lng
        );
        if (min_distance > distance) {
          min_distance = distance;
          this.nearest_rider = this.rider_nearby[i];
        }
      }

      this.setMinDistance(min_distance);
    } else if (this.update_var !== false && toggle == false) {
      this.setMinDistance(false);
    } else {
      this.setMinDistance(false);
      console.log("dynamic update failed");
    }
  }

  /**
   * Enable/Disable & changes im btn text of enter-exit pool button
   * @param {true | false} isEnable - Tells weather to enable or disable the button.
   * @param {String} text - String that will be displayed in the btn
   */
  change_enex_btn(isEnable, text = false) {
    let btn = document.querySelector(".enex-pool-btn");

    if (isEnable) {
      btn.disabled = false;
      btn.style.backgroundColor = "#008000b5";
    } else {
      btn.disabled = true;
      btn.style.backgroundColor = "#6c6c6cb5";
    }

    if (text !== false) {
      btn.innerHTML = text;
    }
  }

  /**
   * Toggles the enter-exit pool button
   * @param {Element} btn - HTML Button element of the enter-exit pool button
   */
  enter_exit_pool_btn(btn) {
    // btn temperorly disabled
    this.change_enex_btn(false);

    if (this.ride_request) {
      this.manage_pool("remove");
    } else {
      this.manage_pool("add");
    }
  }

  manage_pool(request_type) {
    const data = new URLSearchParams({
      request_type: request_type,
      user_type: "passenger",
      username: map.location_self.id.toString(),
    });

    fetch("http://localhost/rideshare/server/manage_pool.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/x-www-form-urlencoded",
      },
      body: data,
    })
      .then((response) => response.json())
      .then((response) => {
        if (response.status === "success") {
          if (request_type == "add") {
            this.ride_request = true;
            this.change_enex_btn(true, "Revoke Request");
            this.cyclic_location_update(true);
            console.log("Added to Pool");
          } else if (request_type == "remove") {
            this.change_enex_btn(true, "Ride Request");
            this.ride_request = false;
            this.cyclic_location_update(false);
            console.log("Removed from Pool");
          } else if (request_type == "check_ifExist") {
            if (response.message == "exist") {
              this.ride_request = true;
              this.change_enex_btn(true, "Revoke Request");
              this.cyclic_location_update(true);
            console.log("Exist in Pool");
            } else if (response.message == "not exist") {
              this.ride_request = false;
              this.change_enex_btn(true, "Ride Request");
              this.cyclic_location_update(false);
              console.log("Not Exist in Pool");
            }
          }
        } else {
          this.enable_enex_btn();
        }
      });
  }

  cyclic_location_update(isStart = false) {
    if(!isStart && this.cycliv_update_var != null) {
      clearInterval(this.cycliv_update_var);
    } else {
      this.cycliv_update_var = setInterval(() => {
        console.log("updates");
        this.socket.send(JSON.stringify(map.location_self));
      }, 2000);
    }
  }

  /**
   * Starting function
   */
  main() {
    map.innitMap();
    // self marker
    map.markUserOnMap(map.location_self, "passenger", false, true);
    // mark passenger on map
    this.setRiderOnMap();
    // start periodic updates on passengers location
    this.update();
    // set enex-pool-btn event
    document.querySelector(".enex-pool-btn").addEventListener("click", () => {
      this.enter_exit_pool_btn(document.querySelector(".enex-pool-btn"));
    });
    // check request pool
    setTimeout(() => this.manage_pool("check_ifExist"), 1000);
  }
}

let passenger = new Main();
window["map"] = map;
window.onload = passenger.main.bind(passenger);