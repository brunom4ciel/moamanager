/*
 *    InstanceStream.java
 *    Copyright (C) 2016 Federal University of Pernambuco, Brazil
 *    @author Silas Garrido (sgtcs@cin.ufpe.br)
 *
 *    This program is free software; you can redistribute it and/or modify
 *    it under the terms of the GNU General Public License as published by
 *    the Free Software Foundation; either version 3 of the License, or
 *    (at your option) any later version.
 *
 *    This program is distributed in the hope that it will be useful,
 *    but WITHOUT ANY WARRANTY; without even the implied warranty of
 *    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *    GNU General Public License for more details.
 *
 *    You should have received a copy of the GNU General Public License
 *    along with this program. If not, see <http://www.gnu.org/licenses/>.
 *    
 */
package moa.streams;

import java.util.List;

/**
 * Interface representing news data stream of instances. 
 *
 * @author Silas Garrido (sgtcs@cin.ufpe.br)
 */
public interface InstanceStreamGenerators extends InstanceStream {
    
     /**
     * Change Random Seed. Change seeds
     * of streams generators at runtime.
     *
     * @param value
     */
    public void changeRandomSeed( int value );
    
     /**
     * Gets the drift position set by user
     *
     * @return drift position set by user
     */
    public List<Integer> getDriftPositions();
    
     /**
     * Gets the drift width set by user
     *
     * @return drift width set by user
     */
    public List<Integer> getDriftWidths();
}
