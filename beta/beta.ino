 #include "TinyGPS.h"
#include <SoftwareSerial.h>

//gps serial port RX, TX
SoftwareSerial gss(9, 10);
SoftwareSerial device(3, 11);

int incomingByte = 0;

//keep track of how long push button was pressed
int state = HIGH;
int reading;
int previous = LOW;
long time = 0;
int debounce = 200; //the debounce time, increase if the output flickers

boolean runGPS = false;

TinyGPS gps;

void setUpdateRate()
{
  device.write((byte)0xA0); //A0
  //device.print(160, HEX);
  delay(200);
  
  device.write((byte)0xA1);  //A1
  //device.print(161, BIN);
  delay(200);
  
  device.write((byte)0x00);    //00
  //device.print(0, BIN);
  delay(200);
  
  device.write((byte)0x03);    //03
  //device.print(3, BIN);
  delay(200);
  
  device.write((byte)0x0E);   //0E
  //device.print(14, BIN);
  delay(200);
  
  device.write((byte)0x05);   //10 10Hz
  //device.print(05, BIN);
  delay(200);
  
  device.write((byte)0x01);
  //device.print(1, HEX);
  delay(200);
  
  device.write((byte)0x0A);
  //device.print(10, HEX);
  delay(200);
  
  device.write((byte)0x0D);
  //device.print(13, BIN);
  delay(200);

  device.write((byte)0x0A);
  //device.print(10, HEX);
  delay(200);
 
  if (gss.available() > 0)
  {
    Serial.print(gss.read(), HEX);
  }
}

void setBaud()
{
  device.write((byte)0xA0); //A0
  
  device.write((byte)0xA1);  //A1
  
  device.write((byte)0x00);    //00
  
  device.write((byte)0x04);    //03
  
  device.write((byte)0x05);   //0E
  
  device.write((byte)0x00);   //10 10Hz
  
  device.write((byte)0x01);
  
  device.write((byte)0x00);
  
  device.write((byte)0x04);
  
  device.write((byte)0xD0);

  device.write((byte)0xA0);
 
  if (gss.available() > 0)
  {
    Serial.print(gss.read(), HEX);
  }
}

void setup()
{
  pinMode(2, INPUT);
  pinMode(3, OUTPUT);
  pinMode(11, OUTPUT);
  
  Serial.begin(9600);
  gss.begin(9600);
  
  attachInterrupt(2, pushButton, CHANGE);
}

void pushButton()
{
  reading = digitalRead(2);
  
  if (reading == HIGH && previous == LOW && millis() - time > debounce)
  {
    if (state == HIGH)
    {
      state = LOW;
      digitalWrite(13, LOW);
      runGPS = false;
    }
    else
    {
      state = HIGH;
      digitalWrite(13, HIGH);
      runGPS = true;
    }
  }
  
  time = millis();
}

void getLocation()
{
  int c = gss.read();
  
  if (gps.encode(c))
  {
    
    long lat, lon;
    unsigned long fix_age, speeds;
    
    gps.get_position(&lat, &lon, &fix_age);
    speeds = gps.speed();
   
    Serial.print("Lat: ");
    Serial.print( lat );
    gss.print("Lat: ");
    gss.print( lat );
    
    Serial.print(", Lon: ");
    Serial.print( lon );
    Serial.print(" -- speed: ");
    Serial.println(speeds);
    gss.print(", Lon: ");
    gss.println( lon );
    gss.print(" -- speed: ");
    gss.println(speeds);
    
  }
  
}

void loop()
{
    while (gss.available())
    {
       getLocation();
    }
}
