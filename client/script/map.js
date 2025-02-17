class Map {
  constructor(location_self) {
    this.location_self = location_self;
    this.collegeLocation = { lat: 30.273257, lng: 77.99925 };
    this.isSelfMarked = false;
    this.nearby_marker = {};
    this.path = false;
  }
  /**
   * This function is used to initilise the google map
   */
  innitMap() {
    this.directionsService = new google.maps.DirectionsService();

    this.map = new google.maps.Map(document.getElementById("map"), {
      zoom: 17,
      center: this.location_self,
    });

    let college_icon = {
      url: `/rideshare/resources/college.png`,
      scaledSize: new google.maps.Size(40, 40),
    };

    this.college_marker = new google.maps.Marker({
      position: this.collegeLocation,
      map: this.map,
      title: "Graphic Era Hill University",
      draggable: false,
      animation: google.maps.Animation.DROP,
      icon: college_icon,
    });
  }

  /**
   * This function finds the route between the starting point and destination.
   * @param {Array(number)} start - Coordinates of starting point
   * @param {Array(number)} end - coordinates of destination
   * @returns Route Object of google map API, meaning the route from start till end
   */
  calculateRoute(start, end) {
    return new Promise((resolve, reject) => {
      this.directionsService.route(
        {
          origin: start,
          destination: end,
          travelMode: google.maps.TravelMode.DRIVING,
        },
        (response, status) => {
          if (status === google.maps.DirectionsStatus.OK) {
            resolve(response.routes[0].overview_path); // Path array (set of lat-lng points)
          } else {
            reject(status);
          }
        }
      );
    });
  }

  /**
   * A helper function to `isOnTheSamePath()` function to calculate distance netween two points
   * @param {number} lat1 - lattitude of point 1
   * @param {number} lon1 - longitude of point 1
   * @param {number } lat2 - lattitude of point 2
   * @param {number} lon2 - longitude of point 2
   * @returns returns distance in KM
   */
  calculateDistance(lat1, lon1, lat2, lon2) {
    const R = 6371; // Radius of the Earth in km
    const dLat = (lat2 - lat1) * (Math.PI / 180);
    const dLon = (lon2 - lon1) * (Math.PI / 180);
    const a =
      Math.sin(dLat / 2) * Math.sin(dLat / 2) +
      Math.cos(lat1 * (Math.PI / 180)) *
        Math.cos(lat2 * (Math.PI / 180)) *
        Math.sin(dLon / 2) *
        Math.sin(dLon / 2);
    const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
    return R * c; // Distance in km
  }

  /**
   * Tells weather two path are same or not.
   * @param {Route_Object} route1 - Route object for path 1
   * @param {Route_Object} route2 - Route object for path 2
   * @returns {Boolean} Weather paths are same or not
   */
  isOnTheSamePath(route1, route2) {
    const maxDistance = 1; // Distance threshold in km
    for (let i = 0; i < Math.min(route1.length, route2.length); i++) {
      const dist = calculateDistance(
        route1[i].lat(),
        route1[i].lng(),
        route2[i].lat(),
        route2[i].lng()
      );
      if (dist > maxDistance) {
        return false; // If the points are too far apart, they're not on the same path
      }
    }
    return true; // If all points are within the threshold distance
  }

  /**
   * This function marks marker or updates the marker of a user's location.
   * @param {Object} user_location - an object containing id, longitude and latitude of an user.
   * @param {'rider' | 'passenger'} type - type of user weather a rider or passenger.
   * @param {boolean} store - weather to store marker object in nearby array or not
   * @param {boolean} self - defines waeather the marker is of user's itself or not
   */
  markUserOnMap(user_location, type, store = false, self = false) {
    let icon = {
      url: `/rideshare/resources/${type}.png`,
      scaledSize: new google.maps.Size(40, 40),
    };

    if (!(user_location.id in this.nearby_marker)) {
      if (this.isSelfMarked && self) {
        return;
      }
      let user_marker = new google.maps.Marker({
        position: user_location,
        map: this.map,
        title: user_location.id.toString(),
        draggable: false,
        animation: google.maps.Animation.DROP,
        icon: icon,
      });

      if (store) {
        this.nearby_marker[user_location.id] = user_marker;
      }
      if (!self) {
        this.isSelfMarked = true;
      }
    } else {
      // update marker
      this.nearby_marker[user_location.id].setPosition(user_location);
    }
  }

  setPath(pointA, pointB) {
    if (this.path === false) {
      this.path = new google.maps.Polyline({
        path: [pointA, pointB],
        geodesic: true,
        strokeColor: "#e35b32",
        strokeOpacity: 1.0,
        strokeWeight: 1,
        icons: [
          {
            icon: {
              path: google.maps.SymbolPath.FORWARD_CLOSED_ARROW,
              strokeOpacity: 0.4,
              scale: 2,
            },
            offset: "0",
            repeat: "30px",
          },
        ],
      });

      this.path.setMap(this.map);
    } else {
      console.log("Path is already set");
    }
  }

  clearPath() {
    if (this.path) {
      this.path.setMap(null);
      this.path = null;
    }
  }
}

export { Map };
