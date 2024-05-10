import React from 'react';
import { getNProducts } from 'mocks/products';
import { View, StyleSheet, FlatList } from 'react-native';
import { scale } from 'react-native-size-matters';
import PropTypes from 'prop-types';
import Colors from 'themes/colors';
import isEmpty from 'lodash/isEmpty';
import BasicTile from './BasicTile';
import ListTile from './ListTile';
import Text from '../Text';
const styles = StyleSheet.create({
  container: {
    flexDirection: 'row',
    flexWrap: 'wrap',
    paddingHorizontal: scale(14),
    paddingVertical: scale(10),
  },
  left: {
    marginLeft: scale(7),
  },
  right: {
    marginRight: scale(7),
  },
  header: {
    flexDirection: 'row',
    alignItems: 'center',
  },
  title: {
    marginTop: scale(20),
  },
  divider: {
    borderBottomWidth: StyleSheet.hairlineWidth,
    borderColor: Colors.gray50,
    flex: 1,
    marginTop: scale(20),
    marginHorizontal: scale(14),
  },
});

const ProductList = ({
  
  title,isfor = null, numberOfProducts, navigation, variant, products,
}) => {

  // const productsList = isEmpty(products) ? getNProducts(numberOfProducts) : products;
  const Tile = variant === 'grid' ? BasicTile : ListTile;
 const renderItem=({item,index})=>{
   return(
    <Tile
    onPress={() => navigation.navigate('Product', { item })}
   key={item.id}
    style={index % 2 === 0 ? styles.right : styles.left}
    {...item}
    />
   )
 }
  return (

    <View>
      {title && (
        <View style={styles.header}>
          <View style={styles.divider} />
          <Text
            weight="medium"
            color="gray75"
            centered
            style={styles.title}
          >
            {title}
          </Text>
          <View style={styles.divider} />
        </View>
      )}
      <View style={StyleSheet.flatten([
        styles.container,
        { flexDirection: variant === 'grid' ? 'row' : 'column' },
        { flexWrap: variant === 'grid' ? 'wrap' : 'nowrap' },
      ])}
      >
        {/* {products.map((product, index) => {
          return(
             <Tile
            onPress={() => navigation.navigate('Product', { product })}
            key={product.id}
            style={index % 2 === 0 ? styles.right : styles.left}
            {...product}
          />
          );
         
      })} */}
      <FlatList
                numColumns={2}
                data={products}
                renderItem={renderItem}
            />
      </View>
    </View>
  );
};

ProductList.propTypes = {
  navigation: PropTypes.object.isRequired,
  numberOfProducts: PropTypes.number,
  title: PropTypes.string,
  isfor: PropTypes.string,
  variant: PropTypes.oneOf(['list', 'grid']),
  products: PropTypes.array,
};

ProductList.defaultProps = {
  numberOfProducts: 10,
  title: null,
  isfor:null,
  variant: 'grid',
  products: [],
};

export default ProductList;
