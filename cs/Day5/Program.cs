using System;
using System.Collections.Generic;
using System.IO;
using System.Linq;

namespace Day5
{
    class Program
    {
        static void Main(string[] args)
        {
            Console.WriteLine("Advent of Code 2020 - Day 5");

            List<string> seatCodes = ReadFrom("input.txt").Select(x => x).ToList();
            List<int> sortedSeatIds = seatCodes.Select(x => GetSeatId(x)).OrderBy(x => x).ToList();

            Console.WriteLine("Part 1: What is the highest seat ID on a boarding pass?");
            Console.WriteLine("Result: " + sortedSeatIds.Last());
            Console.WriteLine("Part 2: What is the ID of your seat?");
            Console.WriteLine("Result: " + GetMySeatId(sortedSeatIds));
        }

        static IEnumerable<string> ReadFrom(string file)
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

        static int GetSeatId(string seatCode)
        {
            int row = GetRow(seatCode);
            int column = GetColumn(seatCode);
            return row * 8 + column;
        }
        static int GetRow(string seatCode)
        {
            var rowBinary = seatCode.Substring(0, 7).Replace('B', '1').Replace('F', '0');
            return Convert.ToInt32(rowBinary, 2);
        }

        static int GetColumn(string seatCode)
        {
            var columnBinary = seatCode.Substring(7, 3).Replace('R', '1').Replace('L', '0');
            return Convert.ToInt32(columnBinary, 2);
        }

        static int GetMySeatId(List<int> sortedSeatIds)
        {
            int lastSeatId = sortedSeatIds.First();
            foreach (int currentSeatId in sortedSeatIds)
            {
                if (currentSeatId - 2 == lastSeatId)
                {
                    return lastSeatId + 1;
                }
                lastSeatId = currentSeatId;
            }
            throw new Exception("No empty seat in list");
        }
    }
}
