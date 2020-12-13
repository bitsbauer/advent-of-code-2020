using System;
using System.Collections.Generic;
using System.IO;
using System.Linq;

namespace Day13
{
    class Program
    {
        static void Main(string[] args)
        {
            Console.WriteLine("Advent of Code 2020 - Day 13");
            
            Console.WriteLine("Part 1: What is the ID of the earliest bus you can take to the airport multiplied by the number of minutes you'll need to wait for that bus?");
            var data = ReadInputForNextDepart("input.txt");
            var nextDepart = NextDepart(data.Item1, data.Item2); // startdepart, buslines
            var result1 = nextDepart.Item1 * nextDepart.Item2; // wait-time * busline
            Console.WriteLine("Result: " + result1);

            Console.WriteLine("Part 2: What is the earliest timestamp such that all of the listed bus IDs depart at offsets matching their positions in the list?");
            var buslines = ReadInputForOffsetDepart("input.txt");
            var result2 = NextOffsetDepart(buslines);
            Console.WriteLine("Result: " + result2);
        }

        protected static Tuple<int, List<int>> ReadInputForNextDepart(string file)
        {
            using (var reader = File.OpenText(file))
            {
                var startDepart = Int32.Parse(reader.ReadLine());
                var buslines = reader.ReadLine().Split(',').Where(x => x != "x").Select(x => Int32.Parse(x)).ToList();
                return new Tuple<int, List<int>> (startDepart, buslines);
            }
        }

        protected static Tuple<int, int> NextDepart(int startDepart, List<int> buslines)
        {
            int i = 0;
            while(i++ < buslines.Max()) {
                var departToCheck = startDepart + i; 
                foreach(int busline in buslines) {
                    if (departToCheck % busline == 0) {
                        return new Tuple<int, int> (i, busline);
                    }
                }
            }
            throw new Exception("No next departure found");
        }

        protected static List<int> ReadInputForOffsetDepart(string file)
        {
            using (var reader = File.OpenText(file))
            {
                reader.ReadLine();
                return reader.ReadLine().Replace("x","1").Split(',').Select(x => Int32.Parse(x)).ToList();
            }
        }

        protected static Int64 NextOffsetDepart(List<int> buslines)
        {
            Int64 factor = buslines[0];
            Int64 timestamp = buslines[0];
            int busPos = 1;
            while(busPos < buslines.Count()) {
                if((timestamp + busPos) % buslines[busPos] == 0) {
                    factor *= buslines[busPos++];
                } else {
                    timestamp += factor;
                }
            }
            return timestamp;
        }
    }
}
