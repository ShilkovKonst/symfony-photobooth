async function getAddresses(eventAddress) {
  try {
    const response = await fetch(
      `https://api-adresse.data.gouv.fr/search/?codeRegion=11&q=${eventAddress}&type=housenumber&autocomplete=1`
    );
    if (response.ok) {
      const addresses = await response.json();
      return addresses.data;
    } else {
      const error = await response.json();
      console.error("No address found", error);
    }
  } catch (error) {
    console.log({ error: error.message });
  }
}

const addressInput = document.getElementById("addressInput")
const addressesList = document.getElementById('addressesList')
const addresses = []
const address = ''

async function fetchAddresses (eventAddress) {
    addresses = await getAddresses(eventAddress)
  }

addressInput.addEventListener("input", function (e) {
  address = e.target.value
  if (address.length > 3) {
    fetchAddresses(address)
  }
});
