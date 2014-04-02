#include "TinyGPS.h"
#include <SoftwareSerial.h>

//gps serial port RX, TX
SoftwareSerial gss(9, 10);
SoftwareSerial device(11, 3);

//keep track of how long push button was pressed
int state = HIGH;
int reading;
int previous = LOW;
long time = 0;
int debounce = 200; //the debounce time, increase if the output flickers

//flags
boolean runGPS = false;

TinyGPS gps;

void createFile(char fname[40])
{
  int wait = 100; //delay to send commands to openlog
  gss.write(26);
  gss.write(26);
  gss.write(26);
  
  
  gss.print("new ");
  gss.print(fname);
  gss.print("/r");
  delay(wait);
  gss.print("append ");
  gss.print(fname);
  gss.print("/r");
  delay(wait);
}

void setGPSBaud()
{
  device.write((byte)160); //A0
  delay(50);
  device.write((byte)161);  //A1
  delay(50);
  device.write((byte)0);    //00
  delay(50);
  device.write((byte)4);
  delay(50);  
  device.write((byte)5);    //messageID
  delay(50);
  device.write((byte)0);    //COM PORT
  delay(50);
  device.write((byte)3);    //BAUD RATE: 38400
  delay(50);
  device.write((byte)0);
  delay(50);
  device.write((byte)5);
  delay(50);
  device.write((byte)13);
  delay(50);
  device.write((byte)10);
  delay(50);
}

void setUpdateRate()
{
  device.write((byte)160); //A0
  delay(50);
  device.write((byte)161);  //A1
  delay(50);
  device.write((byte)0);    //00
  delay(50);
  device.write((byte)3);    //03
  delay(50);
  device.write((byte)14);   //0E
  delay(50);
  device.write((byte)10);   //10 10Hz
  delay(50);
  device.write((byte)0);
  delay(50);
  device.write((byte)15);
  delay(50);
  device.write((byte)13);
  delay(50);
  device.write((byte)10);
  delay(50);
}
void setup()
{
  //delay(10000);
  pinMode(2, INPUT);
  pinMode(3, OUTPUT);
  pinMode(13, OUTPUT);
  
  Serial.begin(9600);
  gss.begin(9600);
  
  setGPSBaud();
  delay(1000);
  setUpdateRate();
  delay(1000);
  
  //attachInterrupt(2, pushButton, CHANGE);
 
  createFile("test.log");
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
