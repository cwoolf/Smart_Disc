//Includes
#include <SoftwareSerial.h>
#include <TinyGPS++.h>

//PINS SETTINGS
const int buttonPin = 2;    // the number of the pushbutton pin
const int ledPin = 13;      // the number of the LED pin

// Variables will change:
int ledState = LOW;         // the current state of the output pin
int ledLast = LOW;
int buttonState;             // the current reading from the input pin
int lastButtonState = LOW;   // the previous reading from the input pin

// the following variables are long's because the time, measured in miliseconds,
// will quickly become a bigger number than can be stored in an int.
long lastDebounceTime = 0;  // the last time the output pin was toggled
long debounceDelay = 50;    // the debounce time; increase if the output flickers

//Serial ports RX, TX
SoftwareSerial gss(10, 11);
SoftwareSerial lss(3, 9);
static const uint32_t Baud = 9600;
int fileNumber;
char buff[50];

TinyGPSPlus gps;

void file()
{
 gss.end();
 delay(100);
 lss.begin(Baud);
 
 //Reset OpenLog
  digitalWrite(A3, LOW);
  delay(100);
  digitalWrite(A3, HIGH);

  //Works with Arduino v1.0
  lss.write(26);
  lss.write(26);
  lss.write(26);
  
  lss.print("new file.txt\r");
  //Wait for OpenLog to return to waiting for a command
 
  //sprintf(buff, "append nate%03d.txt\r", fileNumber);
  lss.print("append sallll.txt\r");
  
  lss.print("ready: ");
  
  lss.end();
  delay(100);
  gss.begin(Baud);
}


void setup() {
  
  gss.begin(Baud);
  
  pinMode(buttonPin, INPUT);
  pinMode(ledPin, OUTPUT);
  pinMode(A4, OUTPUT);
  pinMode(A3, OUTPUT);
  
  randomSeed(analogRead(0));
 
  // set initial LED state
  digitalWrite(ledPin, ledState);
  
  //file();
}

void newCourse()
{
  gss.end();
  lss.begin(9600);
  
  if (ledLast == LOW)
  {
    lss.println("New Course ==========================================");
  }
  else if (ledLast == HIGH)
  {
    lss.println("END =================================================");
    lss.println("");
    lss.println("");
  }
  
  lss.end();
  gss.begin(9600);
}

void pushButton()
{
  // read the state of the switch into a local variable:
  int reading = digitalRead(buttonPin);

  // check to see if you just pressed the button 
  // (i.e. the input went from LOW to HIGH),  and you've waited 
  // long enough since the last press to ignore any noise:  

  // If the switch changed, due to noise or pressing:
  if (reading != lastButtonState) {
    // reset the debouncing timer
    lastDebounceTime = millis();
  } 
  
  if ((millis() - lastDebounceTime) > debounceDelay) {
    // whatever the reading is at, it's been there for longer
    // than the debounce delay, so take it as the actual current state:

    // if the button state has changed:
    if (reading != buttonState) {
      buttonState = reading;

      // only toggle the LED if the new button state is HIGH
      if (buttonState == HIGH) {
        ledLast = ledState;
        ledState = !ledState;
        newCourse();
      }
    }
  }
  
  // set the LED:
  digitalWrite(ledPin, ledState);
  digitalWrite(A4, ledState);

  // save the reading.  Next time through the loop,
  // it'll be the lastButtonState:
  lastButtonState = reading;
}

void loop() {
  
  pushButton();
  
   while (gss.available() > 0 && ledState == HIGH)
   {
    if (gps.encode(gss.read()))
    {
      gss.end();
      lss.begin(Baud);
        displayInfo();
      lss.end();
      gss.begin(Baud);   
    }
   }  
}

void displayInfo()
{
  if (gps.location.isValid())
  {
    
    lss.print(gps.location.lat(), 6);
    lss.print(F(", "));
    lss.print(gps.location.lng(), 6);
    lss.print(F(", "));
    lss.print(gps.altitude.meters());
    lss.print(F(", "));
    lss.print(gps.speed.mps());
    lss.print(F(", "));
    lss.print(gps.speed.mph());
    lss.print(F(", "));
    lss.print(gps.time.hour()-4);
    lss.print(F(":"));
    if (gps.time.minute() < 10) lss.print(F("0"));
    lss.print(gps.time.minute());
    lss.print(F(":"));
    if (gps.time.second() < 10) lss.print(F("0"));
    lss.print(gps.time.second());
   
    
  }
  else
  {
    Serial.print(F("INVALID"));
  }
 

  lss.println();
}
