# 🎵 Jazler Soho PHP Class Parser V1.0

<table>
<tr>
<td width="50%" valign="top">

## 🇬🇷 Περιγραφή

Πρόκειται για μία PHP class με την οποία μπορείτε να δείξετε τα δεδομένα που στέλνει η δημοφιλής εφαρμογή για ραδιοφωνικούς σταθμούς **Jazler SOHO** ([https://jazler.com/soho/](https://jazler.com/soho/)) στην ιστοσελίδα σας.  

Το πακέτο περιλαμβάνει επίσης άλλη μία class η οποία χρησιμοποιεί το **API της ιστοσελίδας greek-radios.gr** για να εμφανίζει πληροφορίες για το εκάστοτε τραγούδι σχετικά με τον καλλιτέχνη ή και τους στίχους του τραγουδιού (**μόνο για Έλληνες καλλιτέχνες και τραγούδια**).  

</td>
<td width="50%" valign="top">

## 🇬🇧 Description

This is a **PHP class** that allows you to display data sent by the popular radio automation software **Jazler SOHO** ([https://jazler.com/soho/](https://jazler.com/soho/)) on your website.  

The package also includes an additional class that uses the **Greek-Radios.gr API** to display song-related information such as the **artist** and **lyrics** (**only for Greek songs and artists**).  

</td>
</tr>
</table>

---

<table>
<tr>
<td width="50%" valign="top">

## 🔧 Χρήση

Για να χρησιμοποιήσετε την class, πρέπει να έχετε εγκατεστημένο το **Jazler SOHO** για τη μετάδοση του ραδιοφωνικού σας προγράμματος.  

Το Jazler μπορεί να ανεβάζει αυτόματα μέσω FTP αρχεία XML στον server της ιστοσελίδας σας, τα οποία περιέχουν **ζωντανές πληροφορίες** για το πρόγραμμά σας.  

Ανατρέξτε στα εγχειρίδια του Jazler εδώ: [https://jazler.com/help/manuals/](https://jazler.com/help/manuals/)

Συνήθη αρχεία XML:
- `NowOnAir.xml` – Τραγούδι που παίζει τώρα  
- `AirPlayNext.xml` – Επόμενα τραγούδια  
- `AirPlayHistory.xml` – Προηγούμενα τραγούδια  

Η PHP class διαβάζει αυτά τα αρχεία και επιστρέφει μία **array** με όλες τις πληροφορίες, την οποία μπορείτε να μετατρέψετε εύκολα σε HTML για εμφάνιση στην ιστοσελίδα σας.  

</td>
<td width="50%" valign="top">

## 🔧 Usage

To use this class, you must already use **Jazler SOHO** for your radio broadcast.  

Jazler can automatically upload XML files via FTP to your server containing **live broadcast information**.  

Refer to the official Jazler manuals here: [https://jazler.com/help/manuals/](https://jazler.com/help/manuals/)

Common XML files:
- `NowOnAir.xml` – Currently playing song  
- `AirPlayNext.xml` – Upcoming songs  
- `AirPlayHistory.xml` – Previously played songs  

This PHP class reads those XML files and outputs an **array** with all the data, which can easily be displayed as HTML on your website.  

</td>
</tr>
</table>

---

<table width="100%">
<tr>
<td width="100%" valign="top">

## 💻 Example Usage

### Basic Class (all data)
```php
include('../src/jazler_class.php');

$timezone = 'UTC'; // Your jazler server timezone
$jClass = new jazlerClass();

$data = $jClass->jazlerMerge(
  "https://yourwebsite.com/NowOnAir.xml",
  "https://yourwebsite.com/AirPlayHistory.xml",
  "https://yourwebsite.com/AirPlayNext.xml",
  $timezone
);
```

### With API (all data)
```php
include('../src/jazler_class_api.php');

$timezone = 'UTC';// Your jazler server timezone
$jClass = new jazlerClassWithAPI();

$data = $jClass->jazlerMerge(
  "https://yourwebsite.com/NowOnAir.xml",
  "https://yourwebsite.com/AirPlayHistory.xml",
  "https://yourwebsite.com/AirPlayNext.xml",
  $timezone,
  'API_TOKEN'
);
```

### Basic Class (playing now data)
```php
include('../src/jazler_class.php');

$timezone = 'UTC'; // Your jazler server timezone
$jClass = new jazlerClass();

$data = $jClass->jazlerNowPlaying(
  "https://yourwebsite.com/NowOnAir.xml",
  "https://yourwebsite.com/AirPlayHistory.xml",
  "https://yourwebsite.com/AirPlayNext.xml",
  $timezone
);
```

### Basic Class (coming up -next- data)
```php
include('../src/jazler_class.php');

$timezone = 'UTC'; // Your jazler server timezone
$jClass = new jazlerClass();

$data = $jClass->jazlerNextPlay(
  "https://yourwebsite.com/NowOnAir.xml",
  "https://yourwebsite.com/AirPlayHistory.xml",
  "https://yourwebsite.com/AirPlayNext.xml",
  $timezone
);
```

### Basic Class (played -history- data)
```php
include('../src/jazler_class.php');

$timezone = 'UTC'; // Your jazler server timezone
$jClass = new jazlerClass();

$data = $jClass->jazlerHistoryPlay(
  "https://yourwebsite.com/NowOnAir.xml",
  "https://yourwebsite.com/AirPlayHistory.xml",
  "https://yourwebsite.com/AirPlayNext.xml",
  $timezone
);
```

</td>
</tr>
</table>

---

<table width="100%">
<tr>
<td width="100%" valign="top">

## 🧩 Demo

- [Default Class Demo](https://www.greek-radios.gr/jazler/tests/jazler.html)  
- [Class + API Demo](https://www.greek-radios.gr/jazler/tests/jazler_api.html)

</td>
</tr>
</table>

---

<table>
<tr>
<td width="50%" valign="top">

## ⚙️ Απαιτήσεις Συστήματος

- PHP **v8.0+**  
- Ενεργό **cURL**  
- Ενεργό **simplexml**

</td>
<td width="50%" valign="top">

## ⚙️ System Requirements

- PHP **v8.0+**  
- **cURL** extension  
- **simplexml** extension  

</td>
</tr>
</table>

---

<table>
<tr>
<td width="50%" valign="top">

## 🔗 Πληροφορίες API

Περισσότερες πληροφορίες σχετικά με το **Greek-Radios.gr API**:  
[https://www.greek-radios.gr/api-info.php](https://www.greek-radios.gr/api-info.php)

</td>
<td width="50%" valign="top">

## 🔗 API Information

For more information about the **Greek-Radios.gr API**, visit:  
[https://www.greek-radios.gr/api-info.php](https://www.greek-radios.gr/api-info.php)

</td>
</tr>
</table>

---

<table>
<tr>
<td width="50%" valign="top">

## 🧠 To Do

- Σύνδεση με άλλα API (π.χ. Last.fm API)  
- Σύνδεση με AI για επιπλέον πληροφορίες  

</td>
<td width="50%" valign="top">

## 🧠 To Do

- Integration with other APIs (e.g., Last.fm API)  
- Integration with AI for richer song and artist information  

</td>
</tr>
</table>

---

**Author:** Webat.gr  
**Version:** 1.0  
**Language:** PHP 8+  
**Website:** [https://www.webat.gr](https://www.webat.gr)
