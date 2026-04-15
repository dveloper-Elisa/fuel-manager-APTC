document.getElementById("showFormBtn").addEventListener("click", function () {
  document.getElementById("popupForm").classList.remove("hidden");
});

document.getElementById("closeFormBtn").addEventListener("click", function () {
  document.getElementById("popupForm").classList.add("hidden");
});

// Close popup when clicking outside the form
document
  .getElementById("popupForm")
  .addEventListener("click", function (event) {
    if (event.target === this) {
      this.classList.add("hidden");
    }
  });

document.getElementById("closeFormBt").addEventListener("click", function () {
  document.getElementById("popupPrice").classList.add("hidden");
});

// UPDATING PRICES WITHIN FORM

document.getElementById("fuelType").addEventListener("change", function () {
  let fuelType = this.value;

  if (fuelType) {
    fetch("get_discount.php?fuelType=" + fuelType)
      .then((response) => response.json())
      .then((data) => {
        document.getElementById("discount").value = data.discount || 0;
        document.getElementById("discount").disabled = false;
      })
      .catch((error) => console.error("Error fetching discount:", error));
  } else {
    document.getElementById("discount").value = "";
    document.getElementById("discount").disabled = true;
  }
});
