using System;
using System.Collections.Generic;
using System.IO;
using System.Linq;

namespace Day12
{
    class Program
    {
        protected static string[] Compass = new string[] {"N","E","S","W"};

        protected class Ship
        {
            public string Direction { get; set; } 
            public int PosX { get; set; }
            public int PosY { get; set; }
            
            public Ship(string direction, int posX, int posY)
            {
                Direction = direction;
                PosX = posX;
                PosY = posY;
            }
        }
        protected class Waypoint
        {
            public int RelativePosX { get; set; }
            public int RelativePosY { get; set; }
            
            public Waypoint(int relativePosX, int relativePosY)
            {
                RelativePosX = relativePosX;
                RelativePosY = relativePosY;
            }
        }

        protected struct Maneuver
        {
            public readonly string Action { get; init; }
            public readonly int Value { get; init; }
            public Maneuver(string action, int value)
            {
                Action = action;
                Value = value;
            }
        }

        static void Main(string[] args)
        {
            Console.WriteLine("Advent of Code 2020 - Day 12");

            var maneuvers = InitManeuvers("input.txt");
            
            Console.WriteLine("Part 1: What is the Manhattan distance between that location and the ship's starting position?");
            var ship = new Ship("E", 0, 0);
            foreach(var maneuver in maneuvers) {
                ExceuteManeuver(ship, maneuver);
            }
            var result1 = Math.Abs(ship.PosX) + Math.Abs(ship.PosY);
            Console.WriteLine("Result: " + result1);

            Console.WriteLine("Part 2: Figure out where the navigation instructions actually lead. What is the Manhattan distance between that location and the ship's starting position?");
            ship = new Ship("E", 0, 0);
            var waypoint = new Waypoint(10, 1);
            foreach(var maneuver in maneuvers) {
                ExceuteWaypointManeuver(ship, waypoint, maneuver);
            }
            var result2 = Math.Abs(ship.PosX) + Math.Abs(ship.PosY);
            Console.WriteLine("Result: " + result2);
        }

        protected static IEnumerable<string> ReadFrom(string file)
        {
            string line;
            using (var reader = File.OpenText(file))
            {
                while ((line = reader.ReadLine()) != null)
                {
                    yield return line;
                }
            }
        }

        protected static List<Maneuver> InitManeuvers(string file)
        {
            var maneuvers = new List<Maneuver>();
            foreach(var line in ReadFrom(file)) {
                maneuvers.Add(
                    new Maneuver(line.Substring(0,1), Int32.Parse(line.Substring(1)))
                );
            }
            return maneuvers;
        }

        protected static void ExceuteManeuver(Ship ship, Maneuver maneuver) {
            switch(maneuver.Action) {
                case "N":
                    ship.PosY += maneuver.Value;
                    return;
                case "S":
                    ship.PosY -= maneuver.Value;
                    return;
                case "E":
                    ship.PosX += maneuver.Value;
                    return;
                case "W":
                    ship.PosX -= maneuver.Value;
                    return;
                case "F":
                    ExceuteManeuver(ship, new Maneuver(ship.Direction, maneuver.Value));
                    return;
                case "L":
                case "R":
                    TurnDirection(ship, maneuver);
                    return;    
            }
        }

        protected static void TurnDirection(Ship ship, Maneuver maneuver)
        {
            var direction = Enumerable.Range(0, 4).Where(i => Compass[i] == ship.Direction).Single();
            if(maneuver.Action == "L") {
                direction += 4 - (maneuver.Value / 90);
            } else {
                direction += maneuver.Value / 90;
            }
            direction = Math.Abs(direction % 4);
            ship.Direction = Compass[direction];
        }

        protected static void ExceuteWaypointManeuver(Ship ship, Waypoint waypoint, Maneuver maneuver) {
            switch(maneuver.Action) {
                case "N":
                    waypoint.RelativePosY += maneuver.Value;
                    return;
                case "S":
                    waypoint.RelativePosY -= maneuver.Value;
                    return;
                case "E":
                    waypoint.RelativePosX += maneuver.Value;
                    return;
                case "W":
                    waypoint.RelativePosX -= maneuver.Value;
                    return;
                case "F":
                    ship.PosX += waypoint.RelativePosX * maneuver.Value;
                    ship.PosY += waypoint.RelativePosY * maneuver.Value;
                    return;
                case "L":
                case "R":
                    RotateWaypoint(waypoint, maneuver);
                    return;    
            }
        }

        protected static void RotateWaypoint(Waypoint waypoint, Maneuver maneuver)
        {
            int rotationsRight = maneuver.Value / 90;
            if(maneuver.Action == "L") {
                rotationsRight = 4 - (maneuver.Value / 90);
            }
            rotationsRight = Math.Abs(rotationsRight % 4);
            for(int i = 0; i < rotationsRight; i++) {
                RotateWaypointRight(waypoint);
            }
        }

        protected static void RotateWaypointRight(Waypoint waypoint)
        {
            var tempX = waypoint.RelativePosX;
            waypoint.RelativePosX = waypoint.RelativePosY;
            waypoint.RelativePosY = -tempX;
        }
    }
}
